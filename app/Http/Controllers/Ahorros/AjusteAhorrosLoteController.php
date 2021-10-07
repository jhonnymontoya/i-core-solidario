<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\AjusteAhorroLote\CreateAjusteAhorroLoteRequest;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Models\Ahorros\AjusteAhorroLote;
use App\Models\Ahorros\DetalleAjusteAhorroLote;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\Tercero;
use App\Traits\ICoreTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Storage;
use Validator;

class AjusteAhorrosLoteController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$ajustesAhorrosLote = AjusteAhorroLote::entidadId()
								->where('estado', '<>', 'ANULADO')
								->orderBy('estado', 'desc')
								->orderBy('fecha_proceso', 'desc')
								->paginate();
		return view('ahorros.ajusteAhorroLote.index')->withProcesos($ajustesAhorrosLote);
	}

	public function create() {
		return view('ahorros.ajusteAhorroLote.create');
	}

	public function store(CreateAjusteAhorroLoteRequest $request) {
		if($this->moduloCerrado(6, $request->fecha_proceso)) {
			return redirect()->back()
				->withErrors(['fecha_proceso' => 'Módulo de ahorros cerrado para la fecha'])
				->withInput();
		}
		$consecutivo = AjusteAhorroLote::whereEntidadId($this->getEntidad()->id)->select(DB::raw("count(id) as consecutivo"))->first();
		$consecutivo = empty($consecutivo) ? 1 : $consecutivo->consecutivo + 1;

		$proceso = new AjusteAhorroLote;

		$proceso->entidad_id = $this->getEntidad()->id;
		$proceso->fill($request->all());
		$proceso->consecutivo_proceso = $consecutivo;
		$proceso->estado = 'PRECARGA';

		$proceso->save();

		Session::flash('message', 'Se ha creado el proceso');

		return redirect()->route('ajusteAhorrosLoteCargarAhorros', ['obj' => $proceso->id]);
	}

	public function cargarAhorros(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de ahorros');
			return redirect('ajusteAhorrosLote');
		}
		return view('ahorros.ajusteAhorroLote.cargarAhorros')->withProceso($obj);
	}

	public function updateCargarAhorros(AjusteAhorroLote $obj, Request $request) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de créditos');
			return redirect()->route('ajusteAhorrosLoteCargarAhorros', ['obj' => $obj->id]);
		}
		Validator::make($request->all(), [
			'archivoAhorros'	=> 'bail|required|file|max:10240|mimetypes:text/plain'
		],
		[
			'archivoAhorros.max'		=> 'El tamaño de :attribute no debe superar 10MB',
			'archivoAhorros.mimetypes'	=> 'El :attribute debe ser un CSV válido'
		], ['archivoAhorros' => 'archivo de crédito'])->validate();

		$rutaArchivoAhorros = $request->file('archivoAhorros')->store('');
		if(!Storage::exists($rutaArchivoAhorros))abort(500);

		$ahorros = Storage::get($rutaArchivoAhorros);
		$ahorros = explode("\n", $ahorros);
		if(count($ahorros) <= 1) {
			Session::flash('error', 'Error: archivo ahorros se encuentra vacio');
			return redirect()->route('ajusteAhorrosLoteCargarAhorros', ['obj' => $obj->id]);
		}
		$modalidadesAhorros = ModalidadAhorro::entidadId($this->getEntidad()->id)->activa()->whereEsReintegrable(1)->get();
		$datosAhorros = collect();
		$errores = collect();
		$fila = 0;
		$cantidadErrores = 0;
		$cantidadCorrectos = 0;
		foreach($ahorros as &$ahorro) {
			if($fila == 0) {
				$fila++;
				continue;
			}
			$ahorro = str_ireplace("\r", "", $ahorro);
			$ahorro = explode(";", $ahorro);
			if(count($ahorro) != 3) {
				$errores->push('El registro ' . $fila . ' no cumple con la estructura.');
				$cantidadErrores++;
			}
			else {
				$ahorro = collect(['socio_id' => $ahorro[0], 'modalidad_ahorro_id' => $ahorro[1], 'valor' => $ahorro[2]]);
				$resultadoValidacion = $this->validar($ahorro);
				if($resultadoValidacion != null) {
					$errores->push('Registro ' . $fila . ': ' . $resultadoValidacion);
					$fila++;
					$cantidadErrores++;
					continue;
				}
				if($ahorro['valor'] == 0) {
					$errores->push('Registro ' . $fila . ': El valor del ajuste no debe ser $0');
					$fila++;
					$cantidadErrores++;
					continue;
				}
				$socio = Tercero::entidadTercero()
					->with('socio')
					->activo()
					->where('numero_identificacion', $ahorro['socio_id'])
					->first();
				if(!optional($socio)->socio) {
					$errores->push('Registro ' . $fila . ': No se encontró socio con el número de identificación');
					$fila++;
					$cantidadErrores++;
					continue;
				}
				$modalidad = $modalidadesAhorros->where(
					'codigo',
					mb_convert_case($ahorro['modalidad_ahorro_id'], MB_CASE_UPPER, "UTF-8")
				)->first();
				if(!$modalidad) {
					$errores->push('Registro ' . $fila . ': No se encontró la modalidad de ahorro');
					$fila++;
					$cantidadErrores++;
					continue;
				}
				$ahorro['socio_id'] = $socio->socio->id;
				$ahorro['modalidad_ahorro_id'] = $modalidad->id;
				$ahorro['valor'] = intval($ahorro['valor']);

				$res = $datosAhorros->search(function($item, $key) use($ahorro){
					$arr = json_decode($item->detalle);
					return $arr->socio_id == $ahorro['socio_id'] && $arr->modalidad_ahorro_id == $ahorro['modalidad_ahorro_id'];
				});

				if($res !== false) {
					$errores->push('Registro ' . $fila . ': Existen dos o más ajustes para un socio con la misma modalidad');
					$fila++;
					$cantidadErrores++;
					continue;
				}

				$detalleAhorro = new DetalleAjusteAhorroLote;
				$detalleAhorro->ajuste_ahorro_lote_id = $obj->id;
				$detalleAhorro->detalle = $ahorro->toJson();
				$datosAhorros->push($detalleAhorro);
				$cantidadCorrectos++;
			}
			$fila++;
		}
		foreach ($datosAhorros as $detalle)$detalle->save();
		if($cantidadCorrectos > 0) {
			$obj->estado = 'CARGADO';
			$obj->save();
		}
		Storage::delete($rutaArchivoAhorros);
		return view('ahorros.ajusteAhorroLote.resumenCarga')->withProceso($obj)
						->withCantidadErrores($cantidadErrores)
						->withCantidadCorrectos($cantidadCorrectos)
						->withDetalleErrores($errores);
	}

	private function validar($ahorro) {
		$errores = null;
		$validador = Validator::make($ahorro->all(), [
				'socio_id'				=> 'bail|required|integer|min:1',
				'modalidad_ahorro_id'	=> 'bail|required|string|min:2|max:10',
				'valor'					=> 'bail|required|integer'
			],
			[
				'socio_id.required'				=> 'La :attribute no encontrado',
				'socio_id.integer'				=> 'La :attribute debe contener un número de identificación válido',
				'socio_id.min'					=> 'La :attribute debe contener un número de identificación válido',
				'modalidad_ahorro_id.integer'	=> 'La :attribute debe contener un valor válido',
				'modalidad_ahorro_id.min'		=> 'La :attribute debe contener un valor válido',
				'valor.integer'					=> 'El :attribute debe contener un valor válido',
			],
			[
				'socio_id'						=> 'identificación',
				'modalidad_ahorro_id'			=> 'modalidad',
				'valor'							=> 'valor'
		]);
		if($validador->fails()) {
			$errores = implode(', ', $validador->errors()->all());
		}
		return $errores;
	}

	public function resumen(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: Proceso en estado diferente a CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		return view('ahorros.ajusteAhorroLote.contabilizar')->withProceso($obj);
	}

	public function anular(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		return view('ahorros.ajusteAhorroLote.confirmarAnulacion')->withProceso($obj);
	}

	public function updateAnular(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		$obj->detallesAjusteAhorroLote()->delete();
		$obj->estado = 'ANULADO';
		$obj->save();
		Session::flash('message', 'Se ha anulado el proceso ' . $obj->consecutivo_proceso);
		return redirect('ajusteAhorrosLote');
	}

	public function limpiar(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		return view('ahorros.ajusteAhorroLote.confirmarLimpiar')->withProceso($obj);
	}

	public function updateLimpiar(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		$obj->detallesAjusteAhorroLote()->delete();
		$obj->estado = 'PRECARGA';
		$obj->save();
		Session::flash('message', 'Se ha limpiado la carga del proceso ' . $obj->consecutivo_proceso);
		return redirect('ajusteAhorrosLote');
	}

	public function contabilizar(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede contabilizar un proceso en estado diferente a CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		return view('ahorros.ajusteAhorroLote.confirmarContabilizar')->withProceso($obj);
	}

	public function updateContabilizar(AjusteAhorroLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede contabilizar un proceso en estado diferente a CARGADO');
			return redirect('ajusteAhorrosLote');
		}
		if(!$obj->esValido()) {
			Session::flash('error', 'Error: ' . $obj->detalle_error);
			return redirect()->route('ajusteAhorrosLoteResumen', $obj->id);
		}

		//Se inicia transaccion
		DB::beginTransaction();
		try
		{
			$tipoComprobante = TipoComprobante::entidadId()->whereCodigo('AJAH')->uso('PROCESO')->first();

			$movimientoTemporal = new MovimientoTemporal;
			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = $obj->descripcion;
			$movimientoTemporal->fecha_movimiento = $obj->fecha_proceso;
			$movimientoTemporal->origen = 'PROCESO';
			$movimientoTemporal->save();

			$serie = 1;
			$detalles = array();
			$detalleAjustesAhorros = $obj->detallesAjusteAhorroLote;
			foreach($detalleAjustesAhorros as $ajuste) {
				$socio = $ajuste->getSocio();
				$modalidadAhorro = $ajuste->getModalidadAhorro();
				$valor = $ajuste->getValor();

				//Se crean los detalles del movimiento temporal
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->tercero_id = $socio->tercero->id;
				$ajuste->tercero_identificacion = $socio->tercero->numero_identificacion;
				$ajuste->tercero = $socio->tercero->nombre;
				$ajuste->cuif_id = $modalidadAhorro->cuenta->id;
				$ajuste->cuif_codigo = $modalidadAhorro->cuenta->codigo;
				$ajuste->cuif_nombre = $modalidadAhorro->cuenta->nombre;
				$ajuste->serie = $serie++;
				$ajuste->fecha_movimiento = $obj->fecha_proceso;
				if($valor > 0) {
					$ajuste->credito = $valor;
					$ajuste->debito = 0;
				}
				else {
					$ajuste->credito = 0;
					$ajuste->debito = -$valor;
				}
				$ajuste->referencia = $modalidadAhorro->codigo . ' - ' . $socio->tercero->numero_identificacion;
				array_push($detalles, $ajuste);
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

			$totalAjuste = $obj->total_valor_ajuste;
			if($totalAjuste > 0) {
				$ajusteContrapartida->credito = 0;
				$ajusteContrapartida->debito = $totalAjuste;
			}
			else {
				$ajusteContrapartida->credito = -$totalAjuste;
				$ajusteContrapartida->debito = 0;
			}
			$ajusteContrapartida->referencia = $obj->referencia;
			array_push($detalles, $ajusteContrapartida);

			$movimientoTemporal->detalleMovimientos()->saveMany($detalles);

			$respuesta = DB::select('exec ahorros.sp_contabilizar_ajustes_ahorros_lote ?, ?', [$obj->id, $movimientoTemporal->id]);
			if(!count($respuesta)) {
				DB::rollBack();
				Session::flash('error', 'Error: Contabilizando el comprobante');
				return redirect()->route('ajusteAhorrosLoteResumen', $obj->id);
			}

			$respuesta = $respuesta[0];

			if(!empty($respuesta->ERROR)) {
				DB::rollBack();
				Session::flash('error', 'Error: ' . $respuesta->MENSAJE);
				return redirect()->route('ajusteAhorrosLoteResumen', $obj->id);
			}
			$idComprobante = $respuesta->MENSAJE;

			foreach($detalleAjustesAhorros as $ajuste) {
				$arr = json_decode($ajuste->detalle);

				$movimientoAhorro = new MovimientoAhorro;
				$movimientoAhorro->entidad_id = $this->getEntidad()->id;
				$movimientoAhorro->socio_id = $arr->socio_id;
				$movimientoAhorro->modalidad_ahorro_id = $arr->modalidad_ahorro_id;
				$movimientoAhorro->movimiento_id = $idComprobante;
				$movimientoAhorro->fecha_movimiento = $obj->fecha_proceso;
				$movimientoAhorro->valor_movimiento = $arr->valor;
				$movimientoAhorro->save();
			}
			if($this->getEntidad()->usa_tarjeta) {
				foreach($detalleAjustesAhorros as $ajuste) {
					$socio = $ajuste->getSocio();
					event(new CalcularAjusteAhorrosVista($socio->id, false));
				}
			}
			DB::commit();
			Session::flash(
				'message',
				'Se ha contabilizado el ajuste de ahorros ' . $obj->consecutivo_proceso
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
			return redirect('ajusteAhorrosLote');
		}
		catch(Exception $e) {
			DB::rollBack();
		}
	}

	public static function routes() {
		Route::get('ajusteAhorrosLote', 'Ahorros\AjusteAhorrosLoteController@index');
		Route::get('ajusteAhorrosLote/create', 'Ahorros\AjusteAhorrosLoteController@create');
		Route::post('ajusteAhorrosLote', 'Ahorros\AjusteAhorrosLoteController@store');
		Route::get('ajusteAhorrosLote/{obj}/cargarAhorros', 'Ahorros\AjusteAhorrosLoteController@cargarAhorros')->name('ajusteAhorrosLoteCargarAhorros');
		Route::put('ajusteAhorrosLote/{obj}/cargarAhorros', 'Ahorros\AjusteAhorrosLoteController@updateCargarAhorros')->name('ajusteAhorrosLoteCargarAhorrosPut');
		Route::get('ajusteAhorrosLote/{obj}/resumen', 'Ahorros\AjusteAhorrosLoteController@resumen')->name('ajusteAhorrosLoteResumen');

		Route::get('ajusteAhorrosLote/{obj}/anular', 'Ahorros\AjusteAhorrosLoteController@anular')->name('ajusteAhorrosLoteAnular');
		Route::put('ajusteAhorrosLote/{obj}/anular', 'Ahorros\AjusteAhorrosLoteController@updateAnular')->name('ajusteAhorrosLotePutAnular');

		Route::get('ajusteAhorrosLote/{obj}/limpiar', 'Ahorros\AjusteAhorrosLoteController@limpiar')->name('ajusteAhorrosLoteLimpiar');
		Route::put('ajusteAhorrosLote/{obj}/limpiar', 'Ahorros\AjusteAhorrosLoteController@updateLimpiar')->name('ajusteAhorrosLotePutLimpiar');

		Route::get('ajusteAhorrosLote/{obj}/contabilizar', 'Ahorros\AjusteAhorrosLoteController@contabilizar')->name('ajusteAhorrosLoteContabilizar');
		Route::put('ajusteAhorrosLote/{obj}/contabilizar', 'Ahorros\AjusteAhorrosLoteController@updateContabilizar')->name('ajusteAhorrosLotePutContabilizar');
	}
}
