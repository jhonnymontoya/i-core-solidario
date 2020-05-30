<?php

namespace App\Http\Controllers\Creditos;

use App\Helpers\FinancieroHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\ReliquidarCredito\EditReliquidarCreditoRequest;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ControlCierreModulo;
use App\Models\General\Tercero;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class ReliquidarCreditoController extends Controller
{
	use ICoreTrait;

	public function __construct(Request $request) {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log("Ingreso a reliquidar créditos con los siguientes parámetros " . json_encode($request->all()), 'CONSULTAR');
		$request->validate([
			'tercero' => [
							'bail',
							'nullable',
							'integer',
							'exists:sqlsrv.general.terceros,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL'
						],
			'fecha'	 =>  [
							'bail',
							'nullable',
							'date_format:"d/m/Y"',
							'modulocerrado:7'
						]
		],["fecha.modulocerrado" => "Módulo de cartera cerrado para la fecha"]);
		$fecha = null;
		if(!empty($request->fecha)) {
			$fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->endOfDay();
			if($this->moduloCerrado(7, $fecha)) {
				Session::flash('error', 'La fecha \'' . $fecha . '\' seleccionada se encuentra en un periodo cerrado');
				$tercero = null;
			}
			else {
				$tercero = Tercero::entidadTercero()->whereId($request->tercero)->first();
			}
		}
		else {
			$tercero = null;
		}

		$creditos = null;
		if($tercero) {
			$creditos = $tercero->solicitudesCreditos()->whereEstadoSolicitud('DESEMBOLSADO')->get();
			foreach ($creditos as $key => $value) {
				if($value->saldoObligacion($fecha) <= 0) {
					unset($creditos[$key]);
				}
			}
		}
		return view('creditos.reliquidacionCredito.index')->withTercero($tercero)->withCreditos($creditos)->withFecha($fecha);
	}

	public function reliquidar(SolicitudCredito $obj, Request $request) {
		$this->log(sprintf("Ingreso a reliquidar el créditos %s con los siguientes parámetros %s", $obj->numero_obligacion, json_encode($request->all())), 'CONSULTAR');
		$this->objEntidad($obj, 'No está autorizado a reliquidar el crédito');
		$request->validate([
			'fechaReliquidacion'	 =>  [
							'bail',
							'required',
							'date_format:"d/m/Y"',
							'modulocerrado:7'
						]
		],["fechaReliquidacion.modulocerrado" => "Módulo de cartera cerrado para la fecha"]);
		$fechaReliquidacion = Carbon::createFromFormat('d/m/Y', $request->fechaReliquidacion)->startOfDay();
		if(is_null($obj->tercero->socio)) {
			Session::flash('error', 'Error al reliquidar el crédito ya que el tercero no es asociado y no tiene pagaduría');
			$redirect = sprintf('reliquidarCredito?tercero=%s&fecha=%s', $obj->tercero_id, $fechaReliquidacion);
			return redirect($redirect);
		}
		if($obj->estado_solicitud != "DESEMBOLSADO") {
			Session::flash('error', 'Estado de obligación no valido');
			$redirect = sprintf('reliquidarCredito?tercero=%s&fecha=%s', $obj->tercero_id, $fechaReliquidacion);
			return redirect($redirect);
		}
		$movimientosFuturos = $obj->movimientosCapitalCredito()->where('fecha_movimiento', '>', $fechaReliquidacion)->count();
		if($movimientosFuturos) {
			Session::flash('error', 'El crédito tiene movimientos posteriores');
			$redirect = sprintf('reliquidarCredito?tercero=%s&fecha=%s', $obj->tercero_id, $fechaReliquidacion);
			return redirect($redirect);
		}
		$listaProgramaciones = array();
		$programaciones = $obj->tercero->socio->pagaduria->calendarioRecaudos()->whereEstado('PROGRAMADO')->get();
		foreach($programaciones as $programacion) {
			$listaProgramaciones[$programacion->fecha_recaudo->format('d/m/Y')] = $programacion->fecha_recaudo;
		}
		$amortizacion = Session::has('amortizacion') ? Session::get('amortizacion') : null;
		return view('creditos.reliquidacionCredito.reliquidar')
					->withCredito($obj)
					->withTercero($obj->tercero)
					->withFecha($fechaReliquidacion)
					->withProgramaciones($listaProgramaciones)
					->withAmortizacion($amortizacion);
	}

	public function update(SolicitudCredito $obj, EditReliquidarCreditoRequest $request) {
		$this->log(
			sprintf(
				"Reliquidó el créditos %s con los siguientes parámetros %s",
				$obj->numero_obligacion,
				json_encode($request->all())
			),
			'ACTUALIZAR'
		);
		$this->objEntidad($obj, 'No está autorizado a reliquidar el crédito');
		if($obj->estado_solicitud != "DESEMBOLSADO") {
			Session::flash('error', 'Estado de obligación no valido');
			$redirect = sprintf(
				'reliquidarCredito?tercero=%s&fecha=%s',
				$obj->tercero_id,
				$fechaAjuste
			);
			return redirect($redirect);
		}
		if($obj->saldoObligacion($request->fechaReliquidacion) <= 0) {
			Session::flash('error', 'Obligación sin saldo para reliquidación');
			$redirect = sprintf(
				'reliquidarCredito?tercero=%s&fecha=%s',
				$obj->tercero_id,
				$fechaAjuste
			);
			return redirect($redirect);
		}
		$formaReliquidar = $request->freliquidar;// 1 = Por plazo, 2 = Por cuota
		$reliquidar = $request->submit=="Previsualizar amortización"?false:true;
		if($formaReliquidar == 1) {//Por plazo
			$plazo = $request->pplazo;
			$cuota = 0;
			$periodicidad = $request->pperiodicidad;
			$fechaProximoPago = $request->pproximoPago;
			$fechapproximoPagoIntereses = $request->pproximoPagoIntereses;
		}
		elseif($formaReliquidar == 2) {//Por cuota
			$plazo = 0;
			$cuota = $request->ccuota;
			$periodicidad = $request->cperiodicidad;
			$fechaProximoPago = $request->cproximoPago;
			$fechapproximoPagoIntereses = $request->cproximoPagoIntereses;
		}
		$nuevasAmortizaciones = FinancieroHelper::reliquidarAmortizacion(
			$obj,
			$request->fechaReliquidacion,
			$formaReliquidar,
			$plazo,
			$cuota,
			$periodicidad,
			$fechaProximoPago,
			$fechapproximoPagoIntereses
		);

		$listaProgramaciones = array();
		$programaciones = $obj
			->tercero
			->socio
			->pagaduria
			->calendarioRecaudos()
			->whereEstado('PROGRAMADO')
			->get();
		foreach($programaciones as $programacion) {
			$listaProgramaciones[$programacion->fecha_recaudo->format('d/m/Y')] = $programacion->fecha_recaudo;
		}
		if($reliquidar) {
			$obj->amortizaciones()->delete();
			$obj->amortizaciones()->saveMany($nuevasAmortizaciones);
			$valorCuota = 0;
			if($formaReliquidar == 2) {
				$valorCuota = $cuota;
			}
			else {
				$saldoCapital = $obj->saldoObligacion($request->fechaReliquidacion);
				$valorCuota = FinancieroHelper::obtenerValorCuota($saldoCapital, $plazo, $obj->tipo_amortizacion, $obj->tasa, $periodicidad);
			}

			$obj->valor_cuota = round($valorCuota);
			$obj->plazo = $nuevasAmortizaciones->count();
			$obj->periodicidad = $periodicidad;
			$obj->save();

			Session::flash('message', 'Se ha reliquidado la obligación ' . $obj->numero_obligacion);
			$redirect = sprintf('reliquidarCredito?tercero=%s&fecha=%s', $obj->tercero_id, $request->fechaReliquidacion);
			return redirect($redirect);
		}
		else {
			Session::flash('amortizacion', $nuevasAmortizaciones);
			return redirect()->back()->withInput();
		}
	}

	public static function routes() {
		Route::get('reliquidarCredito', 'Creditos\ReliquidarCreditoController@index');
		Route::get('reliquidarCredito/{obj}/reliquidar', 'Creditos\ReliquidarCreditoController@reliquidar')->name('reliquidarCreditoReliquidar');
		Route::put('reliquidarCredito/{obj}', 'Creditos\ReliquidarCreditoController@update');
	}
}
