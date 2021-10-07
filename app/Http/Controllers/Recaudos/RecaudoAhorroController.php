<?php

namespace App\Http\Controllers\Recaudos;

use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Controllers\Controller;
use App\Http\Requests\Recaudos\RecaudosAhorros\CreateRecaudoAhorroRequest;
use App\Http\Requests\Recaudos\RecaudosAhorros\MakeRecaudoAhorroRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\Impuesto;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\ControlInteresCartera;
use App\Models\Creditos\ControlSeguroCartera;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Models\Recaudos\RecaudoAhorro;
use App\Models\Socios\Socio;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Route;
use Session;

class RecaudoAhorroController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a abonos con ahorros", $request);
		$recaudos = RecaudoAhorro::entidadId()->orderBy('fecha_recaudo', 'ASC')->paginate();
		return view('recaudos.recaudosAhorros.index')->withRecaudos($recaudos);
	}

	public function create(CreateRecaudoAhorroRequest $request) {
		$this->log("Ingresó a crear nuevo abono con ahorros con los siguientes parámetros " . json_encode($request->all()));
		$fechaConsulta = isset($request["fecha"]) ? Carbon::createFromFormat('d/m/Y', $request["fecha"])->startOfDay() : Carbon::now()->startOfDay();
		$socio = $ahorros = $creditos = $modalidad = null;
		$totalAhorros = 0;
		if(!is_null($request["socio"])) {
			$socio = Socio::find($request["socio"]);
			if($socio) {
				$ahorros = DB::select("exec recaudos.sp_consulta_ahorros ?, ?", [$socio->id, $fechaConsulta]);
				$ahorros = collect($ahorros);
				$totalAhorros = $socio->getTotalAhorros($fechaConsulta);
			}
			$creditos = $socio->tercero->solicitudesCreditos()->whereEstadoSolicitud('DESEMBOLSADO')->get();
			$creditos->each(function($item, $key) use($fechaConsulta, $creditos){
				if($item->fecha_desembolso->gt($fechaConsulta)) {
					$creditos->forget($key);
				}
				else {
					$saldo = $item->saldoObligacion($fechaConsulta->copy()->addYears(1000));
					if($saldo <= 0) {
						$creditos->forget($key);
					}
					else {
						$saldo = $item->saldoObligacion($fechaConsulta);
						$intereses = $item->saldoInteresObligacion($fechaConsulta);
						$seguro = $item->saldoSeguroObligacion($fechaConsulta);
						$recaudo = optional($item->proximoRecaudo())->capital_generado;
						$recaudo = empty($recaudo) ? 0 : $recaudo;
						$incluidoRecaudo = ($saldo - $recaudo) + $intereses + $seguro;
						$item->valorIncluidoRecaudo = $incluidoRecaudo;
					}
				}
			});
		}
		if(!is_null($request["modalidad"])) {
			$modalidad = ModalidadAhorro::find($request["modalidad"]);
			$saldo = 0;
			if(!is_null($socio)) {
				$respuesta = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$socio->id, $modalidad->id, $fechaConsulta]);
				if(!empty($respuesta)) {
					$saldo = $respuesta[0]->saldo;
				}
			}
			$modalidad->saldo = $saldo;
			foreach ($ahorros as $key => $value) {
				if($value->codigo == $modalidad->codigo) {
					$ahorros->forget($key);
				}
			}
		}
		$modalidadesAhorro = ModalidadAhorro::entidadId()
			->where('codigo', '<>', 'APO')
			->whereEsReintegrable(1)
			->orderBy('codigo')
			->get();
		$modalidades = array();
		foreach($modalidadesAhorro as $mod) {
			$modalidades[$mod->id] = $mod->codigo . ' - ' . $mod->nombre;
		}
		return view('recaudos.recaudosAhorros.create')
				->withTercero(optional($socio)->tercero)
				->withSocio($socio)
				->withTotalAhorros($totalAhorros)
				->withAhorros($ahorros)
				->withCreditos($creditos)
				->withModalidad($modalidad)
				->withModalidades($modalidades);
	}

	public function store(MakeRecaudoAhorroRequest $request) {
		$this->log("Creó recaudo por ahorro con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$entidad = $this->getEntidad();
		$tipoComprobante = TipoComprobante::entidadId()->whereCodigo('CA')->uso('PROCESO')->first();
		if($tipoComprobante == null) {
			Session::flash('error', 'Error: No se encuentra el tipo de comprobante contable CA');
			return redirect('recaudosAhorros/create');
		}
		$socio = Socio::find($request->socio);
		$tercero = $socio->tercero;
		$modalidadAbono = ModalidadAhorro::find($request->modalidad);
		$cuif = $modalidadAbono->cuenta;
		$fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->startOfDay();
		$mensaje = "Abono con ahorro del " . $fecha . " para el tercero " . $tercero->nombre_completo;
		$baseGMF = 0;
		DB::beginTransaction();
		try
		{
			$movimientoTemporal = new MovimientoTemporal;
			$movimientoTemporal->entidad_id = $entidad->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $mensaje;
			$movimientoTemporal->fecha_movimiento = $fecha;
			$movimientoTemporal->origen = 'PROCESO';
			$movimientoTemporal->save();

			$serie = 1;
			$detalles = array();
			$movimientoAhorros = array();
			$movimientoCapitalCreditos = array();
			$movimientoInteresCreditos = array();
			$movimientoSeguroCreditos = array();
			$data = json_decode($request->data);

			foreach($data->ahorros as $ahorro) {
				//Si el ahorro está nulo, se continua
				if (empty($ahorro)) continue;
				$modalidad = ModalidadAhorro::find($ahorro->modalidad);

				$ajuste = new DetalleMovimientoTemporal;
				$ajuste->entidad_id = $entidad->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($tercero);
				$ajuste->setCuif($modalidad->cuenta);
				$ajuste->serie = $serie++;
				$ajuste->fecha_movimiento = $fecha;
				$ajuste->credito = $ahorro->valor;
				$ajuste->debito = 0;
				$ajuste->referencia = $modalidad->codigo . ' - ' . $tercero->numero_identificacion;
				array_push($detalles, $ajuste);

				$movimientoAhorro = new MovimientoAhorro;
				$movimientoAhorro->entidad_id = $entidad->id;
				$movimientoAhorro->socio_id = $socio->id;
				$movimientoAhorro->modalidad_ahorro_id = $modalidad->id;
				$movimientoAhorro->fecha_movimiento = $fecha;
				$movimientoAhorro->valor_movimiento = $ahorro->valor;
				array_push($movimientoAhorros, $movimientoAhorro);
			}

			//Se crea colecciones de obligaciones para disparar eventos
			//de actualización de en red
			$obligaciones = collect();
			foreach($data->creditos as $credito) {
				//Si el credito está nulo, se continua
				if (empty($credito)) continue;
				$baseGMF += $credito->total;
				$solicitud = SolicitudCredito::find($credito->id);

				//Se busca la cuenta de parametrización de cartera
				$cuenta = ParametroContable::entidadId()->tipoCartera('CONSUMO');

				if($solicitud->forma_pago == 'CAJA') {
					$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA');
				}
				else {
					$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
				}

				$cuenta = $cuenta->categoriaClasificacion($solicitud->calificacion_obligacion)->first();
				if($cuenta == null) {
					Session::flash('error', 'No se encontró parametrización de clasificación contable para créditos.');
					return redirect('recaudosAhorros/create');
				}

				if($credito->capital) {
					$ajuste = new DetalleMovimientoTemporal;
					$ajuste->entidad_id = $entidad->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->setTercero($tercero);
					$ajuste->setCuif($cuenta->cuentaCapital);
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $fecha;
					$ajuste->credito = $credito->capital;
					$ajuste->debito = 0;
					$ajuste->referencia = $solicitud->numero_obligacion;
					array_push($detalles, $ajuste);

					$movimientoCapital = new MovimientoCapitalCredito;
					$movimientoCapital->solicitud_credito_id = $solicitud->id;
					$movimientoCapital->fecha_movimiento = $fecha;
					$movimientoCapital->valor_movimiento = $credito->capital < 0 ? $credito->capital : -$credito->capital;
					array_push($movimientoCapitalCreditos, $movimientoCapital);
				}
				if($credito->intereses) {
					$ajuste = new DetalleMovimientoTemporal;
					$ajuste->entidad_id = $entidad->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->setTercero($tercero);
					$ajuste->setCuif($cuenta->cuentaInteresesIngreso);
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $fecha;
					$ajuste->credito = $credito->intereses;
					$ajuste->debito = 0;
					$ajuste->referencia = $solicitud->numero_obligacion;
					array_push($detalles, $ajuste);

					$movimientoInteres = new ControlInteresCartera;
					$movimientoInteres->solicitud_credito_id = $solicitud->id;
					$movimientoInteres->fecha_movimiento = $fecha;
					$movimientoInteres->interes_pagado = -$credito->intereses;
					array_push($movimientoInteresCreditos, $movimientoInteres);
				}
				if($credito->seguro) {
					$cuenta = optional(ParametroInstitucional::entidadId()->codigo('CR006')->first())->valor;
					$cuenta = Cuif::entidadId()->codigo(intval($cuenta))->first();
					$ajuste = new DetalleMovimientoTemporal;
					$ajuste->entidad_id = $entidad->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->setTercero($tercero);
					$ajuste->setCuif($cuenta);
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $fecha;
					$ajuste->credito = $credito->seguro;
					$ajuste->debito = 0;
					$ajuste->referencia = $solicitud->numero_obligacion;
					array_push($detalles, $ajuste);

					$movimientoSeguro = new ControlSeguroCartera;
					$movimientoSeguro->solicitud_credito_id = $solicitud->id;
					$movimientoSeguro->fecha_movimiento = $fecha;
					$movimientoSeguro->seguro_causado = 0;
					$movimientoSeguro->seguro_pagado = -$credito->seguro;
					array_push($movimientoSeguroCreditos, $movimientoSeguro);
				}
				$obligaciones->push($solicitud);
			}

			if(intval($data->GMF) != 0) {
				//Se crean los detalles del movimiento temporal GMF
				$ajusteGMF = new DetalleMovimientoTemporal;

				$cuenta = optional(ParametroInstitucional::entidadId()->codigo('SO003')->first())->valor;
				$cuenta = Cuif::entidadId()->codigo(intval($cuenta))->first();

				$ajusteGMF->entidad_id = $entidad->id;
				$ajusteGMF->codigo_comprobante = $tipoComprobante->codigo;
				$ajusteGMF->setTercero($tercero);
				$ajusteGMF->setCuif($cuenta);
				$ajusteGMF->serie = $serie++;
				$ajusteGMF->fecha_movimiento = $fecha;

				$ajusteGMF->credito = intval($data->GMF);
				$ajusteGMF->debito = 0;
				array_push($detalles, $ajusteGMF);
			}

			//Se crean los detalles del movimiento temporal contrapartida
			$ajusteContrapartida = new DetalleMovimientoTemporal;

			$ajusteContrapartida->entidad_id = $entidad->id;
			$ajusteContrapartida->codigo_comprobante = $tipoComprobante->codigo;
			$ajusteContrapartida->setTercero($tercero);
			$ajusteContrapartida->setCuif($cuif);
			$ajusteContrapartida->serie = $serie++;
			$ajusteContrapartida->fecha_movimiento = $fecha;

			$ajusteContrapartida->credito = 0;
			$ajusteContrapartida->debito = intval($data->totalRecaudo);
			$ajusteContrapartida->referencia = $modalidadAbono->codigo . ' - ' . $tercero->numero_identificacion;
			array_push($detalles, $ajusteContrapartida);

			$movimientoTemporal->detalleMovimientos()->saveMany($detalles);

			$respuesta = DB::select('exec ahorros.sp_contabilizar_recaudo_caja ?', [$movimientoTemporal->id]);
			if(!count($respuesta)) {
				DB::rollBack();
				Session::flash('error', 'Error: Contabilizando el comprobante');
				return redirect('recaudosAhorros/create');
			}

			$respuesta = $respuesta[0];

			if(!empty($respuesta->ERROR)) {
				DB::rollBack();
				Session::flash('error', 'Error: ' . $respuesta->MENSAJE);
				return redirect('recaudosAhorros/create');
			}

			foreach($movimientoAhorros as $m) {
				$m->movimiento_id = $respuesta->MOVIMIENTO;
				$m->save();
			}
			foreach($movimientoCapitalCreditos as $m) {
				$m->movimiento_id = $respuesta->MOVIMIENTO;
				$m->save();
			}
			foreach($movimientoInteresCreditos as $m) {
				$m->movimiento_id = $respuesta->MOVIMIENTO;
				$m->save();
			}
			foreach($movimientoSeguroCreditos as $m) {
				$m->movimiento_id = $respuesta->MOVIMIENTO;
				$m->save();
			}

			$movimientoAhorro = new MovimientoAhorro;
			$movimientoAhorro->entidad_id = $entidad->id;
			$movimientoAhorro->socio_id = $socio->id;
			$movimientoAhorro->modalidad_ahorro_id = $modalidadAbono->id;
			$movimientoAhorro->fecha_movimiento = $fecha;
			$movimientoAhorro->valor_movimiento = -intval($data->totalRecaudo);
			$movimientoAhorro->movimiento_id = $respuesta->MOVIMIENTO;
			$movimientoAhorro->save();

			$recaudo = new RecaudoAhorro;
			$recaudo->entidad_id = $entidad->id;
			$recaudo->tercero_id = $tercero->id;
			$recaudo->recaudo_cuif_id = $cuif->id;
			$recaudo->movimiento_id = $respuesta->MOVIMIENTO;
			$recaudo->fecha_recaudo = $fecha;
			$recaudo->recaudo = $request->data;
			$recaudo->save();

			if($baseGMF != 0) {
				$impuesto = Impuesto::with('conceptosImpuestos')
					->entidadId()
					->tipo('NACIONAL')
					->activo()
					->whereNombre('GMF')
					->first();
				if(!empty($impuesto)) {
					$concepto = $impuesto->conceptosImpuestos()->whereNombre('DEPOSITOS DE AHORRO')->activo()->first();
					if(!empty($concepto)){
						$mi = new MovimientoImpuesto;
						$mi->entidad_id = $entidad->id;
						$mi->movimiento_id = $respuesta->MOVIMIENTO;
						$mi->setTercero($tercero);
						$mi->fecha_movimiento = $fecha;
						$mi->impuesto_id = $impuesto->id;
						$mi->concepto_impuesto_id = $concepto->id;
						$mi->setCuif($cuenta);
						$mi->base = $baseGMF;
						$mi->tasa = $concepto->tasa;
						$mi->save();
					}
				}
			}

			if($this->getEntidad()->usa_tarjeta) {
				$obligaciones->each(function($item, $key){
					if ($item->solicitudDeTarjetaHabiente()) {
						event(new SolicitudCreditoAjusteCreado($item->id));
					}
				});

				event(new CalcularAjusteAhorrosVista($socio->id, false));
			}

			DB::commit();
			Session::flash(
				'message',
				'Se ha contabilizado el abono con el documento ' . $respuesta->MENSAJE
			);

			if (empty($respuesta->CODIGOCOMPROBANTE) == false) {
                Session::flash(
                    'codigoComprobante',
                    $respuesta->CODIGOCOMPROBANTE
                );

                Session::flash(
                    'numeroComprobante',
                    $respuesta->NUMEROCOMPROBANTE
                );
            }

			return redirect('recaudosAhorros');
		}
		catch(Exception $e) {
			DB::rollBack();
		}
	}

	public static function routes() {
		Route::get('recaudosAhorros', 'Recaudos\RecaudoAhorroController@index');
		Route::get('recaudosAhorros/create', 'Recaudos\RecaudoAhorroController@create');
		Route::post('recaudosAhorros/create', 'Recaudos\RecaudoAhorroController@store');
	}
}
