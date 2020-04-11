<?php

namespace App\Http\Controllers\Creditos;

use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\AjusteCreditos\CreateAjusteCreditosRequest;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\ControlInteresCartera;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Models\General\Tercero;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class AjusteCreditosController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$tercero = Tercero::entidadTercero()->whereId($request->tercero)->first();
		$creditos = null;
		if($tercero) {
			$creditos = $tercero->solicitudesCreditos()
				->with("modalidadCredito")
				->whereEstadoSolicitud('DESEMBOLSADO')
				->get();
		}
		return view('creditos.ajusteCreditos.index')->withTercero($tercero)->withCreditos($creditos);
	}

	public function getAjuste(SolicitudCredito $obj, Request $request) {
		Validator::make($request->all(),
			['fechaAjuste'		=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7'],
			["fechaAjuste.modulocerrado" => "Módulo de cartera cerrado para la fecha de ajuste"],
			[]
		)->validate();

		if($obj->estado_solicitud != 'DESEMBOLSADO') {
			Session::flash('error', 'Obligación invalida para ajuste');
			return back()->withInput();
		}
		return view('creditos.ajusteCreditos.ajuste')->withObligacion($obj)->withFecha($request->fechaAjuste);
	}

	public function ajuste(SolicitudCredito $obj, CreateAjusteCreditosRequest $request) {
		$this->objEntidad($obj);
		//Se busca el tipo de comprobante, si este no existe o no es de uso 'PROCESO', se muestra un error
		$tipoComprobante = TipoComprobante::uso('PROCESO')->entidadId()->whereCodigo('AJCR')->first();
		if($tipoComprobante == null) {
			Session::flash('error', 'No se encuentra el tipo de comprobante para ajuste de créditos.');
			return redirect('ajusteCreditos');
		}

		//Se consulta la cuenta para la contrapartida
		if(!empty($request->cuifId)) {
			$cuentaContrapartida = Cuif::find($request->cuifId);
			if($cuentaContrapartida == null) {
				Session::flash('error', 'No se encuentró la cuenta para la contrapartida del ajuste de créditos.');
				return redirect('ajusteCreditos');
			}
		}

		//Se inicia transaccion
		DB::beginTransaction();
		try {
			//Se crea el movimiento temporal
			$movimientoTemporal = new MovimientoTemporal;

			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $request->comentarios;
			$movimientoTemporal->fecha_movimiento = $request->fechaAjuste;
			$movimientoTemporal->origen = 'PROCESO';

			//Se guarda el movimiento temporal
			$movimientoTemporal->save();

			$totalAjuste = 0;
			//Se busca la cuenta de parametrización de cartera
			$cuenta = ParametroContable::entidadId()->tipoCartera('CONSUMO');

			if($obj->forma_pago == 'CAJA') {
				$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA');
			}
			else {
				$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
			}

			$cuenta = $cuenta->categoriaClasificacion($obj->calificacion_obligacion)->first();
			if($cuenta == null) {
				Session::flash('error', 'No se encontró parametrización de clasificación contable para créditos.');
				return redirect('ajusteCreditos');
			}

			//Se crean los detalles del movimiento temporal
			if(!empty($request->ajusteCapital)) {
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($obj->tercero);
				$ajuste->setCuif($cuenta->cuentaCapital);
				$ajuste->serie = 1;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $obj->numero_obligacion;
				if($request->naturalezaAjusteCapital == 'AUMENTO') {
					$ajuste->credito = 0;
					$ajuste->debito = $request->ajusteCapital;
					$totalAjuste += $request->ajusteCapital;
				}
				else {
					$ajuste->credito = $request->ajusteCapital;
					$ajuste->debito = 0;
					$totalAjuste -= $request->ajusteCapital;
				}
				$movimientoTemporal->detalleMovimientos()->save($ajuste);
			}

			if(!empty($request->ajusteIntereses)) {
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($obj->tercero);
				$ajuste->setCuif($cuenta->cuentaInteresesIngreso);
				$ajuste->serie = 2;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $obj->numero_obligacion;
				if($request->naturalezaAjusteIntereses == 'AUMENTO') {
					$ajuste->credito = 0;
					$ajuste->debito = $request->ajusteIntereses;
					$totalAjuste += $request->ajusteIntereses;
				}
				else {
					$ajuste->credito = $request->ajusteIntereses;
					$ajuste->debito = 0;
					$totalAjuste += -$request->ajusteIntereses;
				}
				$movimientoTemporal->detalleMovimientos()->save($ajuste);
			}

			if(!empty($request->ajusteSeguroCartera)) {
				$cuentaSeguro = ParametroInstitucional::entidadId()->codigo('CR006')->first();
				if(empty($cuentaSeguro)) {
					Session::flash('error', "No se encuentró el parámetro 'CR006' cuenta seguro de cartera.");
					throw new Exception("No se encuentró el parámetro 'CR006' cuenta seguro de cartera.", 1);
				}
				$cuentaSeguro = Cuif::entidadId()->codigo(intval($cuentaSeguro->valor))->first();
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($obj->tercero);
				$ajuste->setCuif($cuentaSeguro);
				$ajuste->serie = 3;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $obj->numero_obligacion;
				if($request->naturalezaAjusteSeguro == 'AUMENTO') {
					$ajuste->credito = 0;
					$ajuste->debito = $request->ajusteSeguroCartera;
					$totalAjuste += $request->ajusteSeguroCartera;
				}
				else {
					$ajuste->credito = $request->ajusteSeguroCartera;
					$ajuste->debito = 0;
					$totalAjuste += -$request->ajusteSeguroCartera;
				}
				$movimientoTemporal->detalleMovimientos()->save($ajuste);
			}

			//contrapartida si la hay
			if($totalAjuste != 0) {
				$cuentaContrapartida = Cuif::find($request->cuifId);
				if($cuentaContrapartida == null) {
					Session::flash('error', 'No se encuentra la cuenta de la contrapartida.');
					throw new Exception("No se encuentra la cuenta de la contrapartida.", 1);
				}
				$tercero = Tercero::find($request->terceroContrapartidaId);
				if(empty($tercero)){
					Session::flash('error', 'No se encuentró el tercero para la contrapartida.');
					throw new Exception("No se encuentró el tercero para la contrapartida.", 1);
				}
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->setTercero($tercero);
				$ajuste->setCuif($cuentaContrapartida);
				$ajuste->serie = 4;
				$ajuste->fecha_movimiento = $request->fechaAjuste;
				$ajuste->referencia = $request->referencia;
				if($totalAjuste >= 0) {
					$ajuste->credito = abs($totalAjuste);
					$ajuste->debito = 0;
				}
				else {
					$ajuste->credito = 0;
					$ajuste->debito = abs($totalAjuste);
				}
				$movimientoTemporal->detalleMovimientos()->save($ajuste);
			}
			$respuesta = DB::select('exec creditos.sp_grabar_ajuste_credito ?, ?', [$movimientoTemporal->id, $obj->id]);
		
			if ($respuesta[0]->ERROR == '0') {
				if($this->getEntidad()->usa_tarjeta) {
					//Se dispara evento de actualización de obligación en red
					if ($obj->solicitudDeTarjetaHabiente()) {
						event(new SolicitudCreditoAjusteCreado($obj->id));
					}

					event(new CalcularAjusteAhorrosVista($obj->id, true));
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
		$url = sprintf("%s?tercero=%s&fechaAjuste=%s", url('ajusteCreditos'), $obj->tercero->id, $request->fechaAjuste);
		return redirect($url);
	}

	public static function routes() {
		Route::get('ajusteCreditos', 'Creditos\AjusteCreditosController@index');
		Route::get('ajusteCreditos/ajuste/{obj}', 'Creditos\AjusteCreditosController@getAjuste')->name('ajusteCredito');
		Route::put('ajusteCreditos/{obj}', 'Creditos\AjusteCreditosController@ajuste');
	}
}
