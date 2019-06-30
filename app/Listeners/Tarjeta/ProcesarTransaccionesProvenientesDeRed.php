<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\ProcesarTransaccionesProvenientesRed;
use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\General\Tercero;
use App\Models\Tarjeta\LogMovimientoTransaccionRecibido;
use App\Models\Tarjeta\Producto;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Exception;
use Log;

class ProcesarTransaccionesProvenientesDeRed implements ShouldQueue
{

	private $transaccionFallida = false;
	private $tr = null;
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Handle the event.
	 *
	 * @param  object  $event
	 * @return void
	 */
	public function handle(ProcesarTransaccionesProvenientesRed $event)
	{
		$transacciones = LogMovimientoTransaccionRecibido::whereEstaProcesado(0)
			->get();

		foreach ($transacciones as $tr) {
			$producto = Producto::convenio($tr->getConvenio())->first();
			if(!$producto) {
				$mensaje = "Error procesando transacción %s, ya que en " .
					"esta no se encontró el convenio " .
					"(LogMovimientoTransaccionRecibido)";
				$mensaje = sprintf($mensaje, $tr->id);
				Log::error($mensaje);

				$tr->es_erroneo = true;
				$tr->esta_procesado = true;
				$tr->save();
				continue;
			}
			$tipoIdentificacion = $tr->getTipoIdentificacion();
			$tercero = Tercero::entidadTercero($producto->entidad_id)
				->TipoIdentificacionId($tipoIdentificacion->id)
				->whereNumeroIdentificacion($tr->getNumeroIdentificacion())
				->first();

			$tr->entidad_id = $producto->entidad_id;
			$tr->producto_id = $producto->id;
			$tr->tercero_id = $tercero->id;

			$this->tr = null;
			$this->tr = $tr;

			$this->prosesarTransaccion();
		}
	}

	public function prosesarTransaccion()
	{
		/**
		 * Identificar el tipo de transacción
		 * - Aumento de saldo cupo de crédito
		 * - Disminución saldo cupo de crédito
		 * - Solo costo
		 * - Omitir, no hacer nada
		 */

		$data = $this->tr->jsonData();

		if($data->S050 != "0") {
			if ($this->tr->getCostoTransaccion() != 0) {
				$this->transaccionFallida = true;
				$this->prosesarTransaccionSoloCosto();
			}
			else {
				$this->prosesarTransaccionOmitir();
			}
			return;
		}

		switch ($data->S008) {
			//Caso aumento de cupo
			case '00':
			case '01':
			case '02':
			case '03':
			case 'CP':
			case 'CQ':
				$this->prosesarTransaccionAumentoCredito();
				break;

			//Caso disminución de cupo
			case '20':
			case '21':
			case '22':
			case '40':
			case '42':
				$this->prosesarTransaccionDisminucionCredito();
				break;

			//Solo costo
			case '30':
			case '31':
			case '32':
			case '35':
			case '36':
			case '37':
			case '89':
			case 'IBQC':
			case '90':
				$this->prosesarTransaccionSoloCosto();
				break;

			//Omitir, no hacer nada
			case '33':
			case '38':
			case '39':
			case '41':
			case '43':
			case '44':
			case '45':
			case '46':
			case 'C0':
			case 'C1':
			case 'C2':
			case 'C3':
			case 'C3TJ':
			case 'C4':
			case 'C4TJ':
			case 'C5':
			case 'C6':
			case 'C7':
			case 'C8':
			case 'C9':
			case 'C10':
			case 'C11':
			case 'RD':
			case 'PG':
			case 'TC01':
			case 'TC21':
			case 'TC89':
			case 'TC30':
			case 'RC':
			case 'RCTJ':
			case 'RCST':
			case 'RR':
			case 'RT':
			case 'RTTJ':
			case 'RTSJ':
			case 'DR':
			case 'CR':
			case 'PSE2':
			case 'PSE3':
			case 'PSE4':
			default:
				$this->prosesarTransaccionOmitir();
				break;
		}
	}

	private function prosesarTransaccionAumentoCredito()
	{
		$s032 = $this->tr->getValor("S032");
		if ($s032 == 0) { 
			$this->prosesarTransaccionSoloCosto();
			return;
		}
		if ($this->tr->getCostoTransaccion() + $s032 == 0) {
			//Tiene costo cero
			$this->prosesarTransaccionOmitir();
			return;
		}
		$data = $this->tr->jsonData();
		$s037 = $this->tr->getValor("S037");
		$s056 = $this->tr->getValor("S056");
		$t = $this->tr->tercero;
		$p = $this->tr->producto;
		$th = $t->tarjetahabientes()
			->entidadId($t->entidad_id)
			->whereNumeroCuentaCorriente($data->S020)
			->first();
		if (is_null($th)) {
			$s037 = 0;
		}
		else {
			$sc = optional($th)->solicitudCredito;
			if(is_null($sc) || $sc->estado_solicitud == "SALDADO") {
				$s037 = 0;
			}
		}

		try {
			DB::beginTransaction();
			$detalleMovimientos = collect();
			$valores = array();

			//Detalles contables
			$detalleMovimientos->push($this->construirDetalleContable(
				$sc->getParametroContable()->cuentaCapital, //Cuenta
				$s032, //Débito
				0, //Crédito
				$sc->numero_obligacion //Referencia
			));
			$valores[] = $s032;
			if ($s037 > 0 && $s056 > 0) {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					$s037, //Débito
					0, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$valores[] = $s037;
				if ($s037 == $s056){
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						0, //Débito
						$s037 + $s032, //Crédito
						$data->S030 //Referencia
					));
				}
				else {
					$diff = $s056 - $s037;
					$c = $diff > 0 ? $p->egresoComision : $p->ingresoComision;
					$detalleMovimientos->push($this->construirDetalleContable(
						$c, //Cuenta
						$diff > 0 ? $diff : 0, //Débito
						$diff > 0 ? 0 : abs($diff), //Crédito
						$data->S030 //Referencia
					));
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						0, //Débito
						$s056 + $s032, //Crédito
						$data->S030 //Referencia
					));
				}
			}
			else if ($s037 > 0) {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					$s037, //Débito
					0, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->ingresoComision, //Cuenta
					0, //Débito
					$s037, //Crédito
					$data->S030 //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->cuentaCompensacion, //Cuenta
					0, //Débito
					$s032, //Crédito
					$data->S030 //Referencia
				));
				$valores[] = $s037;
			}
			else {
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->cuentaCompensacion, //Cuenta
					0, //Débito
					$s032, //Crédito
					$data->S030 //Referencia
				));
			}
			$mt = $this->guardarDetallesmovimiento($detalleMovimientos);
			$res = DB::select(
				'exec tarjeta.sp_contabilizar_transaccion ?',
				[$mt->id]
			);
			if (!$res || $res[0]->ERROR == 1) {
				$men = "Error contabilizando transacción %s en aumento ";
				$men .= "crédito (%s)";
				throw new Exception(
					printf($mensaje, $this->tr->id, $res[0]->MENSAJE)
				);
			}
			//Se obtiene el id del movimiento contabilizado
			$this->tr->movimiento_id = $res[0]->MENSAJE;
			foreach ($valores as $valor) {
				$mcc = MovimientoCapitalCredito::create([
					'solicitud_credito_id' => $sc->id,
					'movimiento_id' => $res[0]->MENSAJE,
					'fecha_movimiento' => $mt->fecha_movimiento,
					'valor_movimiento' => $valor,
					'origen' => 'VISIONAMOS'
				]);
			}
			//Se reamortiza el crédito
			$this->reliquidarCredito($sc, $mt->fecha_movimiento);
			DB::commit();
		} catch(Exception | \InvalidArgumentException $e) {
			$this->tr->es_erroneo = true;
			DB::rollBack();
			$mensaje = "Error procesando transacción %s en %s, %s";
			$mensaje = sprintf(
				$mensaje,
				$this->tr->id,
				"ProcesarTransaccionesProvenientesDeRed",
				$e->getMessage()
			);
			Log::error($mensaje);
		}
		$this->tr->esta_procesado = true;
		$this->tr->save();
	}

	private function prosesarTransaccionDisminucionCredito()
	{
		$s032 = $this->tr->getValor("S032");
		if ($s032 == 0) { 
			$this->prosesarTransaccionSoloCosto();
			return;
		}
		if ($this->tr->getCostoTransaccion() + $s032 == 0) {
			//Tiene costo cero
			$this->prosesarTransaccionOmitir();
			return;
		}
		$data = $this->tr->jsonData();
		$s037 = $this->tr->getValor("S037");
		$s056 = $this->tr->getValor("S056");
		$t = $this->tr->tercero;
		$p = $this->tr->producto;
		$th = $t->tarjetahabientes()
			->entidadId($t->entidad_id)
			->whereNumeroCuentaCorriente($data->S020)
			->first();
		if (is_null($th)) {
			$s037 = 0;
		}
		else {
			$sc = optional($th)->solicitudCredito;
			if(is_null($sc) || $sc->estado_solicitud == "SALDADO") {
				$s037 = 0;
			}
		}

		try {
			DB::beginTransaction();
			$detalleMovimientos = collect();
			$valores = array();

			//Detalles contables
			if ($s037 > 0 && $s056 > 0)
			{
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					0, //Débito
					$s032, //Crédito
					$sc->numero_obligacion //Referencia
				));
				if ($s037 == $s056) 
				{
					$detalleMovimientos->push($this->construirDetalleContable(
						$sc->getParametroContable()->cuentaCapital, //Cuenta
						$s037, //Débito
						0, //Crédito
						$sc->numero_obligacion //Referencia
					));

					$diff = $s032 - $s037;
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						$diff > 0 ? $diff : 0, //Débito
						$diff > 0 ? 0 : abs($diff), //Crédito
						$sc->numero_obligacion //Referencia
					));
					$valores[] = -$s032;
					$valores[] = $s037;
				}
				else {
					$detalleMovimientos->push($this->construirDetalleContable(
						$sc->getParametroContable()->cuentaCapital, //Cuenta
						$s037, //Débito
						0, //Crédito
						$sc->numero_obligacion //Referencia
					));
					$diff = $s056 - $s037;
					$cc = $diff > 0 ? $p->egresoComision : $p->ingresoComision;
					$detalleMovimientos->push($this->construirDetalleContable(
						$cc, //Cuenta
						$diff > 0 ? $diff : 0, //Débito
						$diff > 0 ? 0 : abs($diff), //Crédito
						$data->S030 //Referencia
					));
					$diff = $s032 - $s056;
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						$diff > 0 ? $diff : 0, //Débito
						$diff > 0 ? 0 : abs($diff), //Crédito
						$data->S030 //Referencia
					));
					$valores[] = -$s032;
					$valores[] = $s037;
				}
			}
			else if ($s037 > 0) {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					0, //Débito
					$s032, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					$s037, //Débito
					0, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->ingresoComision, //Cuenta
					0, //Débito
					$s037, //Crédito
					$data->S030 //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->cuentaCompensacion, //Cuenta
					$s032, //Débito
					0, //Crédito
					$data->S030 //Referencia
				));
				$valores[] = -$s032;
				$valores[] = $s037;
			}
			else {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					0, //Débito
					$s032, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->egresoComision, //Cuenta
					$s056, //Débito
					0, //Crédito
					$data->S030 //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->cuentaCompensacion, //Cuenta
					$s032 - $s056, //Débito
					0, //Crédito
					$data->S030 //Referencia
				));
				$valores[] = -$s032;
			}
			$mt = $this->guardarDetallesmovimiento($detalleMovimientos);
			$res = DB::select(
				'exec tarjeta.sp_contabilizar_transaccion ?',
				[$mt->id]
			);
			if (!$res || $res[0]->ERROR == 1) {
				$men = "Error contabilizando transacción %s en disminucion ";
				$men .= "crédito (%s)";
				throw new Exception(
					printf($mensaje, $this->tr->id, $res[0]->MENSAJE)
				);
			}
			//Se obtiene el id del movimiento contabilizado
			$this->tr->movimiento_id = $res[0]->MENSAJE;
			foreach ($valores as $valor) {
				$mcc = MovimientoCapitalCredito::create([
					'solicitud_credito_id' => $sc->id,
					'movimiento_id' => $res[0]->MENSAJE,
					'fecha_movimiento' => $mt->fecha_movimiento,
					'valor_movimiento' => $valor,
					'origen' => 'VISIONAMOS'
				]);
			}
			//Se reamortiza el crédito
			$this->reliquidarCredito($sc, $mt->fecha_movimiento);
			DB::commit();
		} catch(Exception | \InvalidArgumentException $e) {
			$this->tr->es_erroneo = true;
			DB::rollBack();
			$mensaje = "Error procesando transacción %s en %s, %s";
			$mensaje = sprintf(
				$mensaje,
				$this->tr->id,
				"ProcesarTransaccionesProvenientesDeRed",
				$e->getTraceAsString()
			);
			Log::error($mensaje);
		}
		$this->tr->esta_procesado = true;
		$this->tr->save();
	}

	private function prosesarTransaccionSoloCosto()
	{
		if ($this->tr->getCostoTransaccion() == 0) {
			//Tiene costo cero
			$this->prosesarTransaccionOmitir();
			return;
		}
		$data = $this->tr->jsonData();
		$s037 = $this->tr->getValor("S037");
		$s056 = $this->tr->getValor("S056");
		$t = $this->tr->tercero;
		$p = $this->tr->producto;
		$th = $t->tarjetahabientes()
			->entidadId($t->entidad_id)
			->whereNumeroCuentaCorriente($data->S020)
			->first();

		//Si no se encuentra la cuenta corriente, puede ser porque
		//ejemplo: es un retiro y selecciono cuenta de ahorros cuando
		//es solo una tarjeta de cuenta corriente
		//Intento ubicar el TH por numero de tarjeta
		if (is_null($th)) {
			$numeroTarjeta = $data->S030;
			$th = $t->tarjetahabientes()
				->entidadId($t->entidad_id)
				->whereHas('tarjeta', function($query) use($numeroTarjeta){
					$query->where('numero', $numeroTarjeta);
				})
				->first();
		}

		if (is_null($th)) {
			$s037 = 0;
		}
		else {
			$sc = optional($th)->solicitudCredito;
			if(is_null($sc) || $sc->estado_solicitud == "SALDADO") {
				$s037 = 0;
			}
		}

		try {
			DB::beginTransaction();
			$detalleMovimientos = collect();

			$afectaCapitalCredito = false;

			//Detalles contables
			if ($s037 > 0 && $s056 > 0) {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					$s037, //Débito
					0, //Crédito
					$sc->numero_obligacion //Referencia
				));
				if ($s037 == $s056){                    
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						0, //Débito
						$s037, //Crédito
						$data->S030 //Referencia
					));
					$afectaCapitalCredito = true;
				}
				else {
					$diff = $s056 - $s037;
					$cc = $diff > 0 ? $p->egresoComision : $p->ingresoComision;

					$detalleMovimientos->push($this->construirDetalleContable(
						$cc, //Cuenta
						$diff > 0 ? $diff : 0, //Débito
						$diff > 0 ? 0 : abs($diff), //Crédito
						$data->S030 //Referencia
					));
					$detalleMovimientos->push($this->construirDetalleContable(
						$p->cuentaCompensacion, //Cuenta
						0, //Débito
						$s056, //Crédito
						$data->S030 //Referencia
					));

					$afectaCapitalCredito = true;
				}
			}
			else if ($s037 > 0) {
				$detalleMovimientos->push($this->construirDetalleContable(
					$sc->getParametroContable()->cuentaCapital, //Cuenta
					$s037, //Débito
					0, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->ingresoComision, //Cuenta
					0, //Débito
					$s037, //Crédito
					$sc->numero_obligacion //Referencia
				));
				$afectaCapitalCredito = true;
			}
			else {
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->egresoComision, //Cuenta
					$s056, //Débito
					0, //Crédito
					$data->S030 //Referencia
				));
				$detalleMovimientos->push($this->construirDetalleContable(
					$p->cuentaCompensacion, //Cuenta
					0, //Débito
					$s056, //Crédito
					$data->S030 //Referencia
				));
			}
			//Se crea el movimiento temporal
			$mt = $this->guardarDetallesmovimiento($detalleMovimientos);
			$res = DB::select(
				'exec tarjeta.sp_contabilizar_transaccion ?',
				[$mt->id]
			);
			if (!$res || $res[0]->ERROR == 1) {
				$men = "Error contabilizando transacción %s en solo costo (%s)";
				throw new Exception(
					printf($mensaje, $this->tr->id, $res[0]->MENSAJE)
				);
			}
			//Se obtiene el id del movimiento contabilizado
			$this->tr->movimiento_id = $res[0]->MENSAJE;

			if ($afectaCapitalCredito) {
				$mcc = MovimientoCapitalCredito::create([
					'solicitud_credito_id' => $sc->id,
					'movimiento_id' => $res[0]->MENSAJE,
					'fecha_movimiento' => $mt->fecha_movimiento,
					'valor_movimiento' => $s037,
					'origen' => 'VISIONAMOS' 
				]);
				//Se reamortiza el crédito
				$this->reliquidarCredito($sc, $mt->fecha_movimiento);
			}
			DB::commit();
		} catch(Exception | \InvalidArgumentException $e) {
			$this->tr->es_erroneo = true;
			DB::rollBack();
			$mensaje = "Error procesando transacción %s en %s, %s";
			$mensaje = sprintf(
				$mensaje,
				$this->tr->id,
				"ProcesarTransaccionesProvenientesDeRed",
				$e->getTraceAsString()
			);
			Log::error($mensaje);
		}
		$this->tr->esta_procesado = true;
		$this->tr->save();
	}

	private function prosesarTransaccionOmitir()
	{
		$this->tr->esta_procesado = true;
		$this->tr->save();
	}

	private function construirMovimientoTemporal()
	{
		try
		{
			//Se recupera el comprobante de tarjeta afinidad
			//Este se debe hacer por transacción ya que puede que cada
			//transaccion sea de diferente entidad

			$tipoComprobante = TipoComprobante::entidadId($this->tr->entidad_id)
				->uso('PROCESO')
				->whereCodigo('TA')
				->first();

			$data = $this->tr->jsonData();

			$fecha = Carbon::createFromFormat('Ymd', $data->S001)->startOfDay();

			$descripcion = "";
			if ($this->transaccionFallida) {
				$this->transaccionFallida = false;
				$descripcion = "Operación fallida ";
			}
			$descripcion .= "%s %s %s el %s";
			$data = $this->tr->jsonData();

			$descripcion = sprintf(
				$descripcion,
				$this->descripcionCodigoTransaccion($data->S008),
				$this->canal($data->S018),
				$this->detalleTerminal($data->S01T),
				$fecha->format("d-m-Y")
			);

			//Se crea el movimiento contable temporal
			$movimientoTemporal = new MovimientoTemporal;

			$movimientoTemporal->entidad_id = $this->tr->entidad_id;
			$movimientoTemporal->fecha_movimiento = $fecha;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $descripcion;
			$movimientoTemporal->origen = 'PROCESO';
			return $movimientoTemporal;
		}
		catch(\InvalidArgumentException $e) {
			throw $e;
		}
		return null;
	}

	/**
	 * Obtiene la descripción del tipo de transacción S008
	 *
	 **/
	private function descripcionCodigoTransaccion($codigo)
	{
		$descripcion = null;
		switch ($codigo) {
			case '00':
				$descripcion = "Compra";
				break;
			case '01':
				$descripcion = "Retiro";
				break;
			case '02':
				$descripcion = "Ajuste débito";
				break;
			case '03':
				$descripcion = "Retiro sin tarjeta";
				break;
			case 'CP':
				$descripcion = "Compra POS propio";
				break;
			case 'CQ':
				$descripcion = "Copmpra POS propio sin tarjeta";
				break;
			case '20':
				$descripcion = "Anulación compra POS";
				break;
			case '21':
				$descripcion = "Consignación";
				break;
			case '22':
				$descripcion = "Ajuste crédito";
				break;
			case '40':
				$descripcion = "Transferencia intracooperativa";
				break;
			case '42':
				$descripcion = "Transferencia intracooperativa sin tarjeta";
				break;
			case '30':
			case '31':
				$descripcion = "Consulta de saldo";
				break;
			case '32':
				$descripcion = "Consulta de saldo sin tarjeta";
				break;
			case '35':
			case '36':
				$descripcion = "Consulta movimientos";
				break;
			case '37':
				$descripcion = "Consulta movimientos sin tarjeta";
				break;
			case '89':
			case 'IBQC':
				$descripcion = "Consulta costo transacción";
				break;
			case '90':
				$descripcion = "Cambio de clave";
				break;
			case '33':
			case '38':
			case '39':
			case '41':
			case '43':
			case '44':
			case '45':
			case '46':
			case 'C0':
			case 'C1':
			case 'C2':
			case 'C3':
			case 'C3TJ':
			case 'C4':
			case 'C4TJ':
			case 'C5':
			case 'C6':
			case 'C7':
			case 'C8':
			case 'C9':
			case 'C10':
			case 'C11':
			case 'RD':
			case 'PG':
			case 'TC01':
			case 'TC21':
			case 'TC89':
			case 'TC30':
			case 'RC':
			case 'RCTJ':
			case 'RCST':
			case 'RR':
			case 'RT':
			case 'RTTJ':
			case 'RTSJ':
			case 'DR':
			case 'CR':
			case 'PSE2':
			case 'PSE3':
			case 'PSE4':
			default:
				$descripcion = null;
				break;
		}
		return $descripcion;
	}

	private function canal($codigo)
	{
		$concepto = null;
		switch ($codigo) {
			case '00':
				$concepto = "Oficina";
				break;
			case '01':
				$concepto = "Cajero";
				break;
			case '02':
				$concepto = "POS";
				break;
			case '03':
				$concepto = "IVR";
				break;
			case '04':
				$concepto = "WEB";
				break;
			case '05':
				$concepto = "Banca móvil";
				break;
			case '06':
				$concepto = "Coresponsales cooperativos";
				break;
			default:
				$concepto = null;
				break;
		}
		return $concepto;
	}

	private function detalleTerminal($codigo)
	{
		$detalle = null;
		switch ($codigo) {
			case '10':
				$detalle = "Terminal";
				break;
			case '20':
				$detalle = "Otras redes";
				break;
			case '21':
				$detalle = "Servibanca marca compartida - propio";
				break;
			case '22':
				$detalle = "Servibanca cajeros verdes";
				break;
			case '23':
				$detalle = "Servibanca marca compartida";
				break;
			case '24':
				$detalle = "Servibanca convenio";
				break;
			case '25':
				$detalle = "Servibanca outsourcing";
				break;
			case '26':
			case '27':
				$detalle = "Red coopcentral";
				break;
			case '40':
				$detalle = "Establecimiento de comercio";
				break;
			case '41':
				$detalle = "Terminal en establecimiento de comercio";
				break;
			case '42':
				$detalle = "Terminal propio en comercio";
				break;
			case '43':
				$detalle = "Terminal propio en CNB";
				break;
			case '44':
				$detalle = "Caja extendida";
				break;
			case '45':
				$detalle = "Cooresponsal";
				break;
			case '90':
				$detalle = "Audiorespuesta";
				break;
			case '91':
				$detalle = "";
				break;
			case 'B0':
				$detalle = "";
				break;
			case 'W1':
				$detalle = "PSE";
				break;
			case 'W2':
				$detalle = "Interbancaria";
				break;
			case 'W3':
				$detalle = "Pasarela de pagos";
				break;
			case 'W6':
				$detalle = "Baloto";
				break;
			default:
				$detalle = null;
				break;
		}
		return $detalle;
	}

	private function reliquidarCredito($sc, $fecha)
	{
		$s = optional($sc->tercero)->socio;
		if (!$s) {
			throw new Exception("Tercero no socio", 1);
		}
		$p = $s->pagaduria;
		$plazo = $sc->modalidadCredito->plazo;
		$plazo = ConversionHelper::conversionValorPeriodicidad(
			$plazo,
			$p->periodicidad_pago,
			'MENSUAL'
		);
		$plazo = intval($plazo);
		$cuota = 0;
		$periodicidad = $p->periodicidad_pago;
		$fec = $p->calendarioRecaudos()->whereEstado('PROGRAMADO')->first();
		$fechaProximoPago = $fec->fecha_recaudo;
		$fechapproximoPagoIntereses = $fec->fecha_recaudo;

		$nuevasAmortizaciones = FinancieroHelper::reliquidarAmortizacion(
			$sc,
			$fecha,
			1,
			$plazo,
			$cuota,
			$periodicidad,
			$fechaProximoPago,
			$fechapproximoPagoIntereses
		);

		$sc->amortizaciones()->delete();
		$sc->amortizaciones()->saveMany($nuevasAmortizaciones);
		$valorCuota = 0;
		$saldoCapital = $sc->saldoObligacion("31/12/3000");
		$valorCuota = FinancieroHelper::obtenerValorCuota(
			$saldoCapital,
			$plazo,
			$sc->tipo_amortizacion,
			$sc->tasa,
			$periodicidad
		);

		$sc->valor_cuota = round($valorCuota);
		$sc->plazo = $nuevasAmortizaciones->count();
		$sc->periodicidad = $periodicidad;
		$sc->save();
	}

	private function construirDetalleContable($c, $db = 0, $cr = 0, $ref)
	{
		if ($db == 0 && $cr == 0) {
			throw new Exception("Debito o creodo en cero");            
		}

		$t = $this->tr->tercero;

		$det = new DetalleMovimientoTemporal;
		$det->entidad_id = $this->tr->entidad_id;
		$det->tercero_id = $t->id;
		$det->tercero_identificacion = $t->numero_identificacion;
		$det->tercero = $t->nombre;
		$det->cuif_id = $c->id;
		$det->cuif_codigo = $c->codigo;
		$det->cuif_nombre = $c->nombre;
		$det->debito = $db;
		$det->credito = $cr;
		$det->referencia = $ref;

		return $det;
	}

	private function guardarDetallesmovimiento($dts)
	{
		$mt = $this->construirMovimientoTemporal($this->tr);
		$serie = 1;
		$dts->each(function ($item, $key) use ($mt, $serie){
			$item->codigo_comprobante = $mt->tipoComprobante->codigo;
			$item->serie = $serie++;
			$item->fecha_movimiento = $mt->fecha_movimiento;
		});
		$mt->save();
		$mt->detalleMovimientos()->saveMany($dts->all());
		return $mt;
	}
}
