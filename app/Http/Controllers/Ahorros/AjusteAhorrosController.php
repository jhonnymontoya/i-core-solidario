<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Requests\Ahorros\AjusteAhorros\CreateAjusteAhorrosRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\Tercero;
use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class AjusteAhorrosController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$socio = Socio::with('cuotasObligatorias')->whereId($request->socio)->first();
		$respuesta = array();
		$modalidades = array();
		if($socio) {
			$respuesta = DB::select('exec ahorros.sp_seleccion_modalidades_ahorros ?, ?', [$this->getEntidad()->id, $socio->id]);
		}

		foreach($respuesta as $resp) {
			$modalidades[$resp->id] = $resp->nombre;
		}

		return view('ahorros.ajusteAhorros.index')->withSocio($socio)->withModalidades($modalidades);
	}

	public function ajuste(CreateAjusteAhorrosRequest $request) {
		if($this->moduloCerrado(6, $request->fechaAjuste)) {
			return redirect()->back()
				->withErrors(['fechaAjuste' => 'Módulo de ahorros cerrado para la fecha'])
				->withInput();
		}

		//Se consulta el socio
		$socio = Socio::find($request->socio);
		if($socio == null) {
			Session::flash('error', 'No se encuentra el socio para el ajuste de ahorro.');
			return redirect($volver);
		}
		$volver = sprintf('ajusteAhorros?socio=%s&fechaAjuste=%s', $socio->id, $request->fechaAjuste);
		$valorAhorros = floatval("$request->valorAjuste");			
		$valorAhorros = $request->naturalezaAjusteAhorros == 'AUMENTO' ? $valorAhorros : -$valorAhorros;
		$valorIntereses = floatval("$request->valorAjusteIntereses");			
		$valorIntereses = $request->naturalezaAjusteIntereses == 'AUMENTO' ? $valorIntereses : -$valorIntereses;
		$valorAjuste = $valorAhorros + $valorIntereses;

		//Se busca el tipo de comprobante, si este no existe o no es de uso 'PROCESO', se muestra
		//un error
		$tipoComprobante = TipoComprobante::uso('PROCESO')->whereEntidadId($this->getEntidad()->id)->whereCodigo('AJAH')->first();
		if($tipoComprobante == null) {
			Session::flash('error', 'No se encuentra el tipo de comprobante para ajuste de ahorro.');
			return redirect($volver);
		}

		//Se consulta la modalidad
		$modalidadAhorro = ModalidadAhorro::find($request->modalidadId);
		if($modalidadAhorro == null) {
			Session::flash('error', 'No se encuentró la modalidad de ahorro para el ajuste de ahorro.');
			return redirect($volver);
		}

		//Se consulta la cuenta para la contrapartida
		$cuentaContrapartida = Cuif::find($request->cuifId);
		if($cuentaContrapartida == null && $valorAjuste != 0) {
			Session::flash('error', 'No se encuentró la cuenta para la contrapartida del ajuste de ahorro.');
			return redirect($volver);
		}

		//Se inicia transaccion
		DB::beginTransaction();
		try {
			//Se crea el movimiento temporal
			$movimientoTemporal = new MovimientoTemporal;

			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $request->observaciones;
			$movimientoTemporal->fecha_movimiento = $request->fechaAjuste;
			$movimientoTemporal->origen = 'PROCESO';

			//Se guarda el movimiento temporal
			$movimientoTemporal->save();

			$serie = 0;
			$movimientos = [];

			if($valorAhorros != 0) { //Movimiento para ahorros
				//Se crean los detalles del movimiento temporal de ahorros
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($socio->tercero);
				$ajuste->setCuif($modalidadAhorro->cuenta);
				$ajuste->serie = $serie++;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $modalidadAhorro->codigo . ' - ' . $socio->tercero->numero_identificacion;
				if($request->naturalezaAjusteAhorros == 'AUMENTO') {
					$ajuste->credito = $valorAhorros;
					$ajuste->debito = 0;
				}
				else {
					$ajuste->credito = 0;
					$ajuste->debito = -$valorAhorros;
				}
				$movimientos[] = $ajuste;
			}

			if($valorIntereses != 0) { //Movimiento para intereses
				//Se crean los detalles del movimiento temporal de intereses
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($socio->tercero);
				$ajuste->setCuif($modalidadAhorro->cuentaRendimientoInteresesPorPagar);
				$ajuste->serie = $serie++;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $modalidadAhorro->codigo . ' - ' . $socio->tercero->numero_identificacion;
				if($request->naturalezaAjusteIntereses == 'AUMENTO') {
					$ajuste->credito = $valorIntereses;
					$ajuste->debito = 0;
				}
				else {
					$ajuste->credito = 0;
					$ajuste->debito = -$valorIntereses;
				}
				$movimientos[] = $ajuste;
			}

			if($valorAjuste != 0) { //Movimiento para la contrapartida
				$terceroCP = Tercero::find($request->terceroContrapartidaId);
				//Se crean los detalles del movimiento temporal
				$ajusteContrapartida = new DetalleMovimientoTemporal;

				$ajusteContrapartida->entidad_id = $this->getEntidad()->id;
				$ajusteContrapartida->codigo_comprobante = $tipoComprobante->codigo;
				$ajusteContrapartida->setTercero($terceroCP);
				$ajusteContrapartida->setCuif($cuentaContrapartida);
				$ajusteContrapartida->serie = $serie++;
				$ajusteContrapartida->fecha_movimiento = $request->fechaAjuste;
				$ajusteContrapartida->referencia = $request->referencia;
				if($valorAjuste > 0) {
					$ajusteContrapartida->credito = 0;
					$ajusteContrapartida->debito = $valorAjuste;
				}
				else {
					$ajusteContrapartida->credito = -$valorAjuste;
					$ajusteContrapartida->debito = 0;
				}
				$movimientos[] = $ajusteContrapartida;
			}
			$movimientoTemporal->detalleMovimientos()->saveMany($movimientos);

			$respuesta = DB::select('exec ahorros.sp_grabar_ajuste_ahorro ?, ?, ?, ?', [$movimientoTemporal->id, $modalidadAhorro->id, $valorAhorros, $valorIntereses]);
		
			if($respuesta[0]->ERROR == '0') {
				if($this->getEntidad()->usa_tarjeta) {
					event(new CalcularAjusteAhorrosVista($socio->id, false));
				}
				Session::flash('message', $respuesta[0]->MENSAJE);
				DB::commit();
			}
			else {
				Session::flash('error', $respuesta[0]->MENSAJE);
				DB::rollBack();
			}
			DB::commit();
		}
		catch(Exception $e) {
			DB::rollBack();
		}
		return redirect($volver);
	}

	public function saldosPorModalidad(Request $request) {
		Validator::make($request->all(), [
			'socioId'			=> 'required|exists:sqlsrv.socios.socios,id,deleted_at,NULL',
			'modalidadAhorroId'	=> 'required|exists:sqlsrv.ahorros.modalidades_ahorros,id,deleted_at,NULL',
			'fechaSaldo'		=> 'required|date_format:"d/m/Y"',
		])->validate();

		$fecha = Carbon::createFromFormat('d/m/Y', $request->fechaSaldo)->startOfDay();

		$respuesta = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$request->socioId, $request->modalidadAhorroId, $fecha]);

		$saldoPorModalidad['socioId'] = $respuesta[0]->socio_id;
		$saldoPorModalidad['nombreSocio'] = $respuesta[0]->nombre_socio;
		$saldoPorModalidad['fechaSaldo'] = Carbon::createFromFormat('Y-m-d', $respuesta[0]->fecha_saldo)->startOfDay();
		$saldoPorModalidad['saldo'] = number_format($respuesta[0]->saldo, 0);
		$saldoPorModalidad['intereses'] = number_format($respuesta[0]->intereses, 0);
		$saldoPorModalidad['cuota'] = number_format($respuesta[0]->cuota, 0);
		$saldoPorModalidad['periodicidad'] = $respuesta[0]->periodicidad;
		$saldoPorModalidad['ahorrosSinFormato'] = round($respuesta[0]->saldo, 0);
		$saldoPorModalidad['interesesSinFormato'] = round($respuesta[0]->intereses, 0);
		$saldoPorModalidad['codigoModalidad'] = $respuesta[0]->codigo_modalidad;
		$saldoPorModalidad['nombreModalidad'] = $respuesta[0]->nombre_modalidad;

		return response()->json($saldoPorModalidad);
	}

	public static function routes() {
		Route::get('ajusteAhorros', 'Ahorros\AjusteAhorrosController@index');
		Route::post('ajusteAhorros/ajuste', 'Ahorros\AjusteAhorrosController@ajuste');
		Route::get('ajusteAhorros/saldosPorModalidad', 'Ahorros\AjusteAhorrosController@saldosPorModalidad');
	}
}
