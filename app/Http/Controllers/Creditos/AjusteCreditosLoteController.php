<?php

namespace App\Http\Controllers\Creditos;

use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\AjusteCreditoLote\CreateAjusteCreditoLoteRequest;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\AjusteCreditoLote;
use App\Models\Creditos\ControlInteresCartera;
use App\Models\Creditos\ControlSeguroCartera;
use App\Models\Creditos\DetalleAjusteCreditoLote;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Traits\FonadminTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Storage;
use Validator;

class AjusteCreditosLoteController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$ajustesCreditosLote = AjusteCreditoLote::entidadId()->where('estado', '<>', 'ANULADO')->orderBy('estado', 'desc')->orderBy('fecha_proceso', 'desc')->paginate();
		return view('creditos.ajusteCreditoLote.index')->withProcesos($ajustesCreditosLote);
	}

	public function create() {
		return view('creditos.ajusteCreditoLote.create');
	}

	public function store(CreateAjusteCreditoLoteRequest $request) {
		$consecutivo = AjusteCreditoLote::entidadId()->select(DB::raw("count(id) as consecutivo"))->first();
		$consecutivo = empty($consecutivo) ? 1 : $consecutivo->consecutivo + 1;

		$proceso = new AjusteCreditoLote;

		$proceso->entidad_id = $this->getEntidad()->id;
		$proceso->fill($request->all());
		$proceso->consecutivo_proceso = $consecutivo;
		$proceso->estado = 'PRECARGA';
		$proceso->save();
		Session::flash('message', 'Se ha creado el proceso');
		return redirect()->route('ajusteCreditoLoteCargarCreditos', ['obj' => $proceso->id]);
	}

	public function cargarCreditos(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);

		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de creditos');
			return redirect('ajusteCreditoLote');
		}
		return view('creditos.ajusteCreditoLote.cargarCreditos')->withProceso($obj);
	}

	public function updateCargarCreditos(AjusteCreditoLote $obj, Request $request) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de créditos');
			return redirect()->route('ajusteCreditoLoteCargarCreditos', ['obj' => $obj->id]);
		}
		if($this->moduloCerrado(7, $obj->fecha_proceso)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->route('ajusteCreditoLoteCargarCreditos', ['obj' => $obj->id]);
		}
		Validator::make($request->all(), [
			'archivoCreditos'	=> 'bail|required|file|max:10240|mimetypes:text/plain'
		],
		[
			'archivoCreditos.max'		=> 'El tamaño de :attribute no debe superar 10MB',
			'archivoCreditos.mimetypes'	=> 'El :attribute debe ser un CSV válido'
		], ['archivoCreditos' => 'archivo de crédito'])->validate();

		$rutaArchivoCreditos = $request->file('archivoCreditos')->store('');
		if(!Storage::exists($rutaArchivoCreditos))abort(500);

		$creditos = Storage::get($rutaArchivoCreditos);
		$creditos = explode("\n", $creditos);
		if(count($creditos) <= 1) {
			Session::flash('error', 'Error: archivo creditos se encuentra vacio');
			return redirect()->route('ajusteCreditoLoteCargarCreditos', ['obj' => $obj->id]);
		}
		$datosCreditos = collect();
		$errores = collect();
		$fila = 0;
		$cantidadErrores = 0;
		$cantidadCorrectos = 0;
		foreach($creditos as &$credito) {
			if($fila == 0) {
				$fila++;
				continue;
			}
			$credito = str_ireplace("\r", "", $credito);
			$credito = explode(";", $credito);
			if(count($credito) != 4) {
				$errores->push('El registro ' . $fila . ' no cumple con la estructura.');
				$cantidadErrores++;
			}
			else {
				$credito = collect(['solicitud_credito_id' => trim($credito[0]), 'valor_capital' => trim($credito[1]), 'valor_intereses' => trim($credito[2]), 'valor_seguro' => trim($credito[3])]);
				$resultadoValidacion = $this->validar($credito);
				if($resultadoValidacion != null) {
					$errores->push('Registro ' . $fila . ': ' . $resultadoValidacion);
					$fila++;
					$cantidadErrores++;
					continue;
				}
				if($credito['valor_capital'] == 0 && $credito['valor_intereses'] == 0 && $credito['valor_seguro'] == 0) {
					$errores->push('Registro ' . $fila . ': El valor del ajuste no debe ser $0');
					$fila++;
					$cantidadErrores++;
					continue;
				}
				$solicitudCredito = SolicitudCredito::entidadId($this->getEntidad()->id)->where('numero_obligacion', $credito['solicitud_credito_id'])->first();
				if(!$solicitudCredito) {
					$errores->push('Registro ' . $fila . ': No se encontró la obligación con el número ' . $credito['solicitud_credito_id']);
					$fila++;
					$cantidadErrores++;
					continue;
				}
				if($solicitudCredito->estado_solicitud != 'DESEMBOLSADO') {
					$errores->push('Registro ' . $fila . ': Estado de obligación ' . $credito['solicitud_credito_id'] . ' no válido');
					$fila++;
					$cantidadErrores++;
					continue;
				}
				if(empty($solicitudCredito->seguro_cartera_id)) {
					if(!empty($credito['valor_seguro'])) {
						$errores->push('Registro ' . $fila . ': Obligación ' . $credito['solicitud_credito_id'] . ' sin seguro de cartera con ajuste');
						$fila++;
						$cantidadErrores++;
						continue;
					}
				}
				$credito['solicitud_credito_id'] = $solicitudCredito->id;

				$res = $datosCreditos->search(function($item, $key) use($credito){
					$arr = json_decode($item->detalle);
					return $arr->solicitud_credito_id == $credito['solicitud_credito_id'];
				});

				if($res !== false) {
					$errores->push('Registro ' . $fila . ': Existen dos o más ajustes para la obligación ' . $solicitudCredito->numero_obligacion);
					$fila++;
					$cantidadErrores++;
					continue;
				}

				$detalleCredito = new DetalleAjusteCreditoLote;
				$detalleCredito->ajuste_credito_lote_id = $obj->id;
				$detalleCredito->detalle = $credito->toJson();
				$datosCreditos->push($detalleCredito);
				$cantidadCorrectos++;
			}
			$fila++;
		}
		foreach ($datosCreditos as $detalle)$detalle->save();
		if($cantidadCorrectos > 0) {
			$obj->estado = 'CARGADO';
			$obj->save();
		}
		Storage::delete($rutaArchivoCreditos);
		return view('creditos.ajusteCreditoLote.resumenCarga')->withProceso($obj)
						->withCantidadErrores($cantidadErrores)
						->withCantidadCorrectos($cantidadCorrectos)
						->withDetalleErrores($errores);
	}

	private function validar($credito) {
		$errores = null;
		$validador = Validator::make($credito->all(), [
				'solicitud_credito_id'				=> 'bail|required|integer|min:1',
				'valor_capital'						=> 'bail|required|integer',
				'valor_intereses'					=> 'bail|required|integer',
				'valor_seguro'						=> 'bail|required|integer'
			],
			[
				'solicitud_credito_id.required'	=> ':attribute no encontrado',
				'solicitud_credito_id.integer'	=> 'El :attribute debe contener un número de obligación válido',
				'solicitud_credito_id.min'		=> 'El :attribute debe contener un número de obligación válido',
				'valor_capital.integer'			=> 'El :attribute debe contener un valor de capital válido',
				'valor_intereses.integer'		=> 'El :attribute debe contener un valor de intereses válido',
				'valor_seguro.integer'			=> 'El :attribute debe contener un valor de seguro válido',
			],
			[
				'solicitud_credito_id'		=> 'Número de obligación',
				'valor_capital'				=> 'valor capital',
				'valor_intereses'			=> 'valor intereses',
				'valor_seguro'				=> 'valor seguro',
		]);
		if($validador->fails()) {
			$errores = implode(', ', $validador->errors()->all());
		}
		return $errores;
	}

	public function resumen(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: Proceso en estado diferente a CARGADO');
			return redirect('ajusteCreditoLote');
		}
		return view('creditos.ajusteCreditoLote.contabilizar')->withProceso($obj);
	}

	public function anular(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('ajusteCreditoLote');
		}
		return view('creditos.ajusteCreditoLote.confirmarAnulacion')->withProceso($obj);
	}

	public function updateAnular(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('ajusteCreditoLote');
		}
		$obj->detallesAjusteCreditoLote()->delete();
		$obj->estado = 'ANULADO';
		$obj->save();
		Session::flash('message', 'Se ha anulado el proceso ' . $obj->consecutivo_proceso);
		return redirect('ajusteCreditoLote');
	}

	public function limpiar(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('ajusteCreditoLote');
		}
		return view('creditos.ajusteCreditoLote.confirmarLimpiar')->withProceso($obj);
	}

	public function updateLimpiar(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('ajusteCreditoLote');
		}
		$obj->detallesAjusteCreditoLote()->delete();
		$obj->estado = 'PRECARGA';
		$obj->save();
		Session::flash('message', 'Se ha limpiado la carga del proceso ' . $obj->consecutivo_proceso);
		return redirect()->route('ajusteCreditoLoteCargarCreditos', $obj->id);
	}

	public function contabilizar(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede contabilizar un proceso en estado diferente a CARGADO');
			return redirect('ajusteCreditoLote');
		}
		if($this->moduloCerrado(7, $obj->fecha_proceso)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->route('ajusteCreditoLoteResumen', ['obj' => $obj->id]);
		}
		return view('creditos.ajusteCreditoLote.confirmarContabilizar')->withProceso($obj);
	}

	public function updateContabilizar(AjusteCreditoLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede contabilizar un proceso en estado diferente a CARGADO');
			return redirect('ajusteCreditoLote');
		}
		if($this->moduloCerrado(7, $obj->fecha_proceso)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->route('ajusteCreditoLoteResumen', ['obj' => $obj->id]);
		}
		if(!$obj->esValido()) {
			Session::flash('error', 'Error: ' . $obj->detalle_error);
			return redirect()->route('ajusteCreditoLoteResumen', $obj->id);
		}

		//Se crea colecciones de obligaciones para disparar eventos
		//de actualización de en red
		$obligaciones = collect();

		//Se inicia transaccion
		DB::beginTransaction();
		try {
			$tipoComprobante = TipoComprobante::entidadId()->whereCodigo('AJCR')->uso('PROCESO')->first();

			$movimientoTemporal = new MovimientoTemporal;
			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $obj->descripcion;
			$movimientoTemporal->fecha_movimiento = $obj->fecha_proceso;
			$movimientoTemporal->origen = 'PROCESO';
			$movimientoTemporal->save();

			$serie = 1;
			$valorTotal = 0;
			$detalles = array();
			$movimientosCapital = array();
			$movimientosIntereses = array();
			$movimientosSeguros = array();
			$detalleAjustesCreditos = $obj->detallesAjusteCreditoLote;
			foreach($detalleAjustesCreditos as $ajuste) {
				$obligacion = $ajuste->getSolicitudCredito();
				$tercero = $ajuste->getTercero();
				$valorCapital = $ajuste->getValorCapital();
				$valorIntereses = $ajuste->getValorIntereses();
				$valorSeguro = $ajuste->getValorSeguro();
				$valorTotal += $ajuste->getValorTotal();

				//Se busca la cuenta de parametrización de cartera
				$cuenta = ParametroContable::entidadId($this->getEntidad()->id)->tipoCartera('CONSUMO');
				if($obligacion->forma_pago == 'CAJA') {
					$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA');
				}
				else {
					$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
				}
				$cuenta = $cuenta->categoriaClasificacion($obligacion->calificacion_obligacion)->first();

				if($cuenta == null) {
					Session::flash('error', 'No se encontró parametrización de clasificación contable para créditos.');
					DB::rollBack();
					return redirect()->route('ajusteCreditoLoteResumen', $obj->id);
				}

				$cuentaSeguro = ParametroInstitucional::entidadId($this->getEntidad()->id)->codigo('CR006')->first();
				if($cuentaSeguro) {
					$cuentaSeguro = Cuif::entidadId($this->getEntidad()->id)->activa()->codigo($cuentaSeguro->valor)->first();
				}

				//Se crean los detalles del movimiento temporal para capital
				if($valorCapital != 0) {
					$ajuste = new DetalleMovimientoTemporal;

					$ajuste->entidad_id = $this->getEntidad()->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->tercero_id = $tercero->id;
					$ajuste->tercero_identificacion = $tercero->numero_identificacion;
					$ajuste->tercero = $tercero->nombre;
					$ajuste->cuif_id = $cuenta->cuentaCapital->id;
					$ajuste->cuif_codigo = $cuenta->cuentaCapital->codigo;
					$ajuste->cuif_nombre = $cuenta->cuentaCapital->nombre;
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $obj->fecha_proceso;
					if($valorCapital > 0) {
						$ajuste->credito = 0;
						$ajuste->debito = $valorCapital;
					}
					else {
						$ajuste->credito = -$valorCapital;
						$ajuste->debito = 0;
					}
					$ajuste->referencia = $obligacion->numero_obligacion;
					array_push($detalles, $ajuste);

					$movimientoCapitalCredito = new MovimientoCapitalCredito;
					$movimientoCapitalCredito->solicitud_credito_id = $obligacion->id;
					$movimientoCapitalCredito->fecha_movimiento = $obj->fecha_proceso;
					$movimientoCapitalCredito->valor_movimiento = $valorCapital;
					array_push($movimientosCapital, $movimientoCapitalCredito);
				}

				//Se crean los detalles del movimiento temporal para intereses
				if($valorIntereses != 0) {
					$ajuste = new DetalleMovimientoTemporal;

					$ajuste->entidad_id = $this->getEntidad()->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->tercero_id = $tercero->id;
					$ajuste->tercero_identificacion = $tercero->numero_identificacion;
					$ajuste->tercero = $tercero->nombre;
					$ajuste->cuif_id = $cuenta->cuentaInteresesIngreso->id;
					$ajuste->cuif_codigo = $cuenta->cuentaInteresesIngreso->codigo;
					$ajuste->cuif_nombre = $cuenta->cuentaInteresesIngreso->nombre;
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $obj->fecha_proceso;
					if($valorIntereses > 0) {
						$ajuste->credito = 0;
						$ajuste->debito = $valorIntereses;
					}
					else {
						$ajuste->credito = -$valorIntereses;
						$ajuste->debito = 0;
					}
					$ajuste->referencia = $obligacion->numero_obligacion;
					array_push($detalles, $ajuste);

					$controlInteresCartera = new ControlInteresCartera;
					$controlInteresCartera->solicitud_credito_id = $obligacion->id;
					$controlInteresCartera->fecha_movimiento = $obj->fecha_proceso;
					$controlInteresCartera->interes_pagado = $valorIntereses;
					$controlInteresCartera->interes_causado = 0;
					array_push($movimientosIntereses, $controlInteresCartera);
				}

				//Se crean los detalles del movimiento temporal para intereses
				if($valorSeguro != 0) {
					if($cuentaSeguro == null) {
						Session::flash('error', 'No se encontró cuenta contable de seguro de cartera.');
						DB::rollBack();
						return redirect()->route('ajusteCreditoLoteResumen', $obj->id);
					}
					$ajuste = new DetalleMovimientoTemporal;

					$ajuste->entidad_id = $this->getEntidad()->id;
					$ajuste->codigo_comprobante = $tipoComprobante->codigo;
					$ajuste->tercero_id = $tercero->id;
					$ajuste->tercero_identificacion = $tercero->numero_identificacion;
					$ajuste->tercero = $tercero->nombre;
					$ajuste->cuif_id = $cuentaSeguro->id;
					$ajuste->cuif_codigo = $cuentaSeguro->codigo;
					$ajuste->cuif_nombre = $cuentaSeguro->nombre;
					$ajuste->serie = $serie++;
					$ajuste->fecha_movimiento = $obj->fecha_proceso;
					if($valorSeguro > 0) {
						$ajuste->credito = 0;
						$ajuste->debito = $valorSeguro;
					}
					else {
						$ajuste->credito = -$valorSeguro;
						$ajuste->debito = 0;
					}
					$ajuste->referencia = $obligacion->numero_obligacion;
					array_push($detalles, $ajuste);

					$controlSeguroCartera = new ControlSeguroCartera;
					$controlSeguroCartera->solicitud_credito_id = $obligacion->id;
					$controlSeguroCartera->fecha_movimiento = $obj->fecha_proceso;
					$controlSeguroCartera->seguro_pagado = $valorSeguro;
					$controlSeguroCartera->seguro_causado = 0;
					array_push($movimientosSeguros, $controlSeguroCartera);
				}
				$obligaciones->push($obligacion);
			}
			//Se crean los detalles del movimiento temporal contrapartida
			$ajusteContrapartida = new DetalleMovimientoTemporal;

			$ajusteContrapartida->entidad_id = $this->getEntidad()->id;
			$ajusteContrapartida->codigo_comprobante = $tipoComprobante->codigo;
			$ajusteContrapartida->tercero_id = $obj->tercero->id;
			$ajusteContrapartida->tercero_identificacion = $obj->tercero->numero_identificacion;
			$ajusteContrapartida->tercero = $obj->tercero->nombre;
			$ajusteContrapartida->cuif_id = $obj->cuif->id;
			$ajusteContrapartida->cuif_codigo = $obj->cuif->codigo;
			$ajusteContrapartida->cuif_nombre = $obj->cuif->nombre;
			$ajusteContrapartida->serie = $serie++;
			$ajusteContrapartida->fecha_movimiento = $obj->fecha_proceso;

			if ($valorTotal > 0) {
				$ajusteContrapartida->credito = $valorTotal;
				$ajusteContrapartida->debito = 0;
			}
			else {
				$ajusteContrapartida->credito = 0;
				$ajusteContrapartida->debito = -$valorTotal;
			}
			$ajusteContrapartida->referencia = $obj->referencia;
			array_push($detalles, $ajusteContrapartida);

			$movimientoTemporal->detalleMovimientos()->saveMany($detalles);

			$respuesta = DB::select('exec creditos.sp_contabilizar_ajustes_creditos_lote ?, ?', [$obj->id, $movimientoTemporal->id]);
			if (!count($respuesta)) {
				DB::rollBack();
				Session::flash('error', 'Error: Contabilizando el comprobante');
				return redirect()->route('ajusteCreditoLoteResumen', $obj->id);
			}
			if (!empty($respuesta[0]->ERROR)) {
				DB::rollBack();
				Session::flash('error', 'Error: ' . $respuesta[0]->MENSAJE);
				return redirect()->route('ajusteCreditoLoteResumen', $obj->id);
			}
			$idComprobante = $respuesta[0]->MENSAJE;

			foreach ($movimientosCapital as $movimiento) {
				$movimiento->movimiento_id = $idComprobante;
				$movimiento->save();
			}
			foreach ($movimientosIntereses as $movimiento) {
				$movimiento->movimiento_id = $idComprobante;
				$movimiento->save();
			}
			foreach ($movimientosSeguros as $movimiento) {
				$movimiento->movimiento_id = $idComprobante;
				$movimiento->save();
			}

			if($this->getEntidad()->usa_tarjeta) {
				$obligaciones->each(function($item, $key){
					if ($item->solicitudDeTarjetaHabiente()) {
						event(new SolicitudCreditoAjusteCreado($item->id));
					}
					event(new CalcularAjusteAhorrosVista($item->id, true));
				});
			}

			DB::commit();
			Session::flash('message', 'Se ha contabilizado el ajuste de creditos ' . $obj->consecutivo_proceso);
			return redirect('ajusteCreditoLote');
		}
		catch(Exception $e)
		{
			DB::rollBack();
		}
	}

	public static function routes() {
		Route::get('ajusteCreditoLote', 'Creditos\AjusteCreditosLoteController@index');
		Route::get('ajusteCreditoLote/create', 'Creditos\AjusteCreditosLoteController@create');
		Route::post('ajusteCreditoLote', 'Creditos\AjusteCreditosLoteController@store');
		Route::get('ajusteCreditoLote/{obj}/cargarCreditos', 'Creditos\AjusteCreditosLoteController@cargarCreditos')->name('ajusteCreditoLoteCargarCreditos');
		Route::put('ajusteCreditoLote/{obj}/cargarCreditos', 'Creditos\AjusteCreditosLoteController@updateCargarCreditos')->name('ajusteCreditoLoteCargarCreditosPut');
		Route::get('ajusteCreditoLote/{obj}/resumen', 'Creditos\AjusteCreditosLoteController@resumen')->name('ajusteCreditoLoteResumen');

		Route::get('ajusteCreditoLote/{obj}/anular', 'Creditos\AjusteCreditosLoteController@anular')->name('ajusteCreditoLoteAnular');
		Route::put('ajusteCreditoLote/{obj}/anular', 'Creditos\AjusteCreditosLoteController@updateAnular')->name('ajusteCreditoLotePutAnular');

		Route::get('ajusteCreditoLote/{obj}/limpiar', 'Creditos\AjusteCreditosLoteController@limpiar')->name('ajusteCreditoLoteLimpiar');
		Route::put('ajusteCreditoLote/{obj}/limpiar', 'Creditos\AjusteCreditosLoteController@updateLimpiar')->name('ajusteCreditoLotePutLimpiar');

		Route::get('ajusteCreditoLote/{obj}/contabilizar', 'Creditos\AjusteCreditosLoteController@contabilizar')->name('ajusteCreditoLoteContabilizar');
		Route::put('ajusteCreditoLote/{obj}/contabilizar', 'Creditos\AjusteCreditosLoteController@updateContabilizar')->name('ajusteCreditoLotePutContabilizar');
	}
}
