<?php

namespace App\Http\Controllers\Recaudos;

use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Controllers\Controller;
use App\Http\Requests\Recaudos\RecaudosCaja\MakeRecaudoCajaRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\ControlInteresCartera;
use App\Models\Creditos\ControlSeguroCartera;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Models\General\Tercero;
use App\Models\Recaudos\RecaudoCaja;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Route;
use Session;

class RecaudoCajaController extends Controller
{
    use ICoreTrait;

    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request) {
        $this->logActividad("Ingresó a recaudos por caja", $request);
        $recaudos = RecaudoCaja::entidadId()->orderBy('fecha_recaudo', 'DESC')->paginate();
        return view('recaudos.recaudosCaja.index')->withRecaudos($recaudos);
    }

    public function create(Request $request) {
        $entidad = $this->getEntidad();
        $req = $request->validate([
            'tercero'   => 'bail|nullable|integer|exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
            'cuenta'    => 'bail|nullable|integer|exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,1,deleted_at,NULL',
            'fecha'     => 'bail|nullable|date_format:"d/m/Y"'
        ]);
        $this->log("Ingresó a crear nuevo recaudo por caja con los siguientes parámetros " . json_encode($request->all()));
        $fechaConsulta = isset($req["fecha"]) ? Carbon::createFromFormat('d/m/Y', $req["fecha"])->startOfDay() : Carbon::now()->startOfDay();
        $tercero = null; $socio = null; $ahorros = null; $creditos = null;
        $cuenta = null; $cuotasNoReembolsables = null;
        $totalAhorros = 0;
        if(isset($req["tercero"])) {
            $tercero = Tercero::find($req["tercero"]);
            $socio = optional($tercero)->socio;
            if($socio) {
                $ahorros = DB::select(
                    "exec recaudos.sp_consulta_ahorros ?, ?",
                    [$socio->id, $fechaConsulta]
                );
                $ahorros = collect($ahorros);

                $cuotasNoReembolsables = DB::select(
                    "exec recaudos.sp_consulta_ahorros_no_reintegrables ?",
                    [$socio->id]
                );
                $cuotasNoReembolsables = collect($cuotasNoReembolsables);

                $totalAhorros = $socio->getTotalAhorros($fechaConsulta);
            }
            $creditos = $tercero->solicitudesCreditos()->whereEstadoSolicitud('DESEMBOLSADO')->get();
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
        if(isset($req["cuenta"])) {
            $cuenta = Cuif::find($req["cuenta"]);
        }
        return view('recaudos.recaudosCaja.create')
                ->withTercero($tercero)
                ->withSocio($socio)
                ->withTotalAhorros($totalAhorros)
                ->withAhorros($ahorros)
                ->withCreditos($creditos)
                ->withCuotasNoReembolsables($cuotasNoReembolsables)
                ->withCuenta($cuenta);
    }

    public function store(MakeRecaudoCajaRequest $request) {
        $this->log("Creó recaudo por recibo de caja con los siguientes parámetros " . json_encode($request->all()), "CREAR");
        $entidad = $this->getEntidad();
        $tipoComprobante = TipoComprobante::entidadId()->whereCodigo('RC')->uso('PROCESO')->first();
        if($tipoComprobante == null){
            Session::flash('error', 'Error: No se encuentra el tipo de comprobante contable RC');
            return redirect('recaudosCaja/create');
        }
        $tercero = Tercero::find($request->tercero);
        $cuif = Cuif::find($request->cuenta);
        $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->startOfDay();
        $mensaje = "Abono por caja del " . $fecha . " para el tercero " . $tercero->nombre_completo;
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
                $movimientoAhorro->socio_id = $tercero->socio->id;
                $movimientoAhorro->modalidad_ahorro_id = $modalidad->id;
                $movimientoAhorro->fecha_movimiento = $fecha;
                $movimientoAhorro->valor_movimiento = $ahorro->valor;
                array_push($movimientoAhorros, $movimientoAhorro);
            }

            foreach($data->cuotasNoRembolsables as $cuotaNoReembolsable) {
                $modalidad = ModalidadAhorro::find($cuotaNoReembolsable->modalidad);

                $ajuste = new DetalleMovimientoTemporal;
                $ajuste->entidad_id = $entidad->id;
                $ajuste->codigo_comprobante = $tipoComprobante->codigo;
                $ajuste->setTercero($tercero);
                $ajuste->setCuif($modalidad->cuenta);
                $ajuste->serie = $serie++;
                $ajuste->fecha_movimiento = $fecha;
                $ajuste->credito = $cuotaNoReembolsable->valor;
                $ajuste->debito = 0;
                $ajuste->referencia = $modalidad->codigo . ' - ' . $tercero->numero_identificacion;
                array_push($detalles, $ajuste);
            }

            //Se crea colecciones de obligaciones para disparar eventos
            //de actualización de en red
            $obligaciones = collect();
            foreach($data->creditos as $credito) {
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
                    return redirect('recaudosCaja/create');
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

            //Se crean los detalles del movimiento temporal contrapartida
            $ajusteContrapartida = new DetalleMovimientoTemporal;

            $ajusteContrapartida->entidad_id = $entidad->id;
            $ajusteContrapartida->codigo_comprobante = $tipoComprobante->codigo;
            $ajusteContrapartida->setTercero($tercero);
            $ajusteContrapartida->setCuif($cuif);
            $ajusteContrapartida->serie = $serie++;
            $ajusteContrapartida->fecha_movimiento = $fecha;

            $ajusteContrapartida->credito = 0;
            $ajusteContrapartida->debito = $data->totalRecaudo;
            array_push($detalles, $ajusteContrapartida);

            $movimientoTemporal->detalleMovimientos()->saveMany($detalles);

            $respuesta = DB::select('exec ahorros.sp_contabilizar_recaudo_caja ?', [$movimientoTemporal->id]);
            if(!count($respuesta)) {
                DB::rollBack();
                Session::flash('error', 'Error: Contabilizando el comprobante');
                return redirect()->route('recaudosCaja/create');
            }

            $respuesta = $respuesta[0];

            if(!empty($respuesta->ERROR)) {
                DB::rollBack();
                Session::flash('error', 'Error: ' . $respuesta->MENSAJE);
                return redirect()->route('recaudosCaja/create');
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

            $recaudo = new RecaudoCaja;
            $recaudo->entidad_id = $entidad->id;
            $recaudo->tercero_id = $tercero->id;
            $recaudo->recaudo_cuif_id = $cuif->id;
            $recaudo->movimiento_id = $respuesta->MOVIMIENTO;
            $recaudo->fecha_recaudo = $fecha;
            $recaudo->recaudo = $request->data;
            $recaudo->save();

            if($this->getEntidad()->usa_tarjeta) {
                $obligaciones->each(function($item, $key){
                    if ($item->solicitudDeTarjetaHabiente()) {
                        event(new SolicitudCreditoAjusteCreado($item->id));
                    }
                });

                $socio = optional($tercero)->socio;
                if(!is_null($socio)) {
                    event(new CalcularAjusteAhorrosVista($socio->id, false));
                }
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

            return redirect('recaudosCaja');
        }
        catch(Exception $e) {
            DB::rollBack();
        }
    }

    public static function routes() {
        Route::get('recaudosCaja', 'Recaudos\RecaudoCajaController@index');
        Route::get('recaudosCaja/create', 'Recaudos\RecaudoCajaController@create');
        Route::post('recaudosCaja/create', 'Recaudos\RecaudoCajaController@store');
    }
}
