<?php

namespace App\Http\Controllers\Creditos;

use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Http\Controllers\Controller;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Requests\Creditos\ProcesoCreditoLote\CreateProcesoCreditosLoteRequest;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\DetalleProcesoCreditoLote;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\ProcesoCreditosLote;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Tercero;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Storage;
use Validator;

class ProcesoCreditoLoteController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log("Ingresó a solicitudes de crédito en lote", 'INGRESAR');
		$res = $request->validate([
			"name" => "nullable|string|max:50",
			"modalidad" => [
				"nullable",
				"integer",
				"exists:sqlsrv.creditos.modalidades,id,entidad_id,"
				. $this->getEntidad()->id . ",deleted_at,NULL",
			]
		]);
		$res = collect($res);

		$modalidadesCredito = Modalidad::entidadId()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad) {
			if($modalidad->estaParametrizada() == false) {
				continue;
			}
			$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;
		}

		$procesoCreditosLote = ProcesoCreditosLote::with('modalidad')
			->entidadId()
			->orderBy('id', 'desc');

		if ($res->has("name")) {
			$procesoCreditosLote->search($res->get("name"));
		}
		if ($res->has("modalidad") && $res->get("modalidad") > 0) {
			$procesoCreditosLote->whereModalidadCreditoId(
				$res->get("modalidad")
			);
		}
		$procesoCreditosLote = $procesoCreditosLote->paginate();
		return view('creditos.procesoCreditoLote.index')
			->withProcesos($procesoCreditosLote)
			->withModalidades($modalidades);
	}

	public function create() {
		$this->log("Ingresó a crear nueva solicitud de crédito en lote", 'INGRESAR');
		$modalidadesCredito = Modalidad::entidadId()->activa()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad) {
			if($modalidad->estaParametrizada() == false) {
				continue;
			}
			$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;
		}
		return view('creditos.procesoCreditoLote.create')->withModalidades($modalidades);
	}

	public function store(CreateProcesoCreditosLoteRequest $request) {
		$msg = "Creó la solicitud de crédito en lote con los siguientes parámetros %s";
		$msg = sprintf($msg, json_encode($request->all()));
		$this->log($msg, 'CREAR');
		$consecutivo = ProcesoCreditosLote::entidadId()->select(DB::raw("count(id) as consecutivo"))->first();
		if(empty($consecutivo)) {
			$consecutivo = 1;
		}
		else {
			$consecutivo = $consecutivo->consecutivo + 1;
		}

		$proceso = new ProcesoCreditosLote;

		$proceso->fill($request->all());
		$proceso->entidad_id = $this->getEntidad()->id;
		$proceso->consecutivo_proceso = $consecutivo;
		$proceso->estado = 'PRECARGA';

		$proceso->save();

		Session::flash('message', 'Se ha creado el proceso');

		return redirect()->route('procesoCreditoLoteCargarCreditos', ['obj' => $proceso->id]);
	}

	public function cargarCreditos(ProcesoCreditosLote $obj) {
		$msg = "Ingresó a cargar créditos de solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'INGRESAR');
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de créditos');
			return redirect('procesoCreditoLote');
		}
		return view('creditos.procesoCreditoLote.cargarCreditos')->withProceso($obj);
	}

	public function updateCargarCreditos(ProcesoCreditosLote $obj, Request $request) {
		$msg = "Cargó archivo créditos para solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'ACTUALIZAR');
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA') {
			Session::flash('error', 'Error: ya se ha cargado un archivo de créditos');
			return redirect()->route('procesoCreditoLoteCargarCreditos', ['obj' => $obj->id]);
		}
		Validator::make($request->all(), [
			'archivoCredito'	=> 'bail|required|file|max:10240|mimetypes:text/plain'
		],
		[
			'archivoCredito.max'		=> 'El tamaño de :attribute no debe superar 10MB',
			'archivoCredito.mimetypes'	=> 'El :attribute debe ser un CSV válido'
		], ['archivoCredito' => 'archivo de crédito'])->validate();

		$rutaArchivoCreditos = $request->file('archivoCredito')->store('');
		if(!Storage::exists($rutaArchivoCreditos))abort(500);

		$creditos = Storage::get($rutaArchivoCreditos);
		$creditos = explode("\n", $creditos);
		if(count($creditos) <= 1) {
			Session::flash('error', 'Error: archivo crédito se encuentra vacio');
			return redirect()->route('procesoCreditoLoteCargarCreditos', ['obj' => $obj->id]);
		}
		$modalidadCredito = $obj->modalidad;
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
			if(count($credito) != 3) {
				$errores->push('El registro ' . $fila . ' no cumple con la estructura.');
				$cantidadErrores++;
			}
			else {
				$credito = collect(['tercero_id' => $credito[0], 'valor_credito' => $credito[1], 'plazo' => $credito[2]]);
				$resultadoValidacion = $this->validar($credito);
				if($resultadoValidacion != null) {
					$errores->push('Registro ' . $fila . ': ' . $resultadoValidacion);
					$fila++;
					continue;
				}

				$respuestaSolicitudCredito = $this->construirSolicitudCredito($obj, $modalidadCredito, $credito);
				if($respuestaSolicitudCredito instanceof SolicitudCredito) {
					$detalleCreditoLote = new DetalleProcesoCreditoLote;
					$detalleCreditoLote->proceso_credito_lote_id = $obj->id;
					$detalleCreditoLote->solicitud_credito = $respuestaSolicitudCredito;
					$detalleCreditoLote->condiciones = '';
					$datosCreditos->push($detalleCreditoLote);
					$cantidadCorrectos++;
				}
				else {
					$cantidadErrores++;
					$errores->push('Registro ' . $fila . ': ' . $respuestaSolicitudCredito);
				}
			}
			$fila++;
		}
		foreach ($datosCreditos as $detalle)$detalle->save();
		if($cantidadCorrectos > 0) {
			$obj->estado = 'CARGADO';
			$obj->save();
		}
		Storage::delete($rutaArchivoCreditos);
		return view('creditos.procesoCreditoLote.resumenCarga')->withProceso($obj)
						->withCantidadErrores($cantidadErrores)
						->withCantidadCorrectos($cantidadCorrectos)
						->withDetalleErrores($errores);
	}

	private function construirSolicitudCredito($procesoCreditosLote, $modalidadCredito, $credito) {
		$usuario = $this->getUser();
		$error = null;
		$tercero = Tercero::entidadTercero()->whereNumeroIdentificacion($credito['tercero_id'])->first();
		if($tercero == null) {
			$error = 'No se encontró tercero con el número de identificación ' . $credito['tercero_id'];
			return $error;
		}
		if(!$tercero->es_asociado || $tercero->socio == null) {
			$error = 'Tercero ' . $tercero->nombre_corto . ' no es asociado';
			return $error;
		}

		$pagaduria = $tercero->socio->pagaduria()->with('calendarioRecaudos')->first();
		if($pagaduria == null) {
			$error = 'Tercero ' . $tercero->nombre_corto . ' no es asociado';
			return $error;
		}
		$calendario = $pagaduria->calendarioRecaudos->where('estado', 'PROGRAMADO')->sortBy('fecha_recaudo')->first();
		if($calendario == null) {
			$error = 'No hay programación de recaudos para ' . $tercero->nombre_corto;
			return $error;
		}
		$solicitudCredito = new SolicitudCredito;
		$solicitudCredito->entidad_id = $this->getEntidad()->id;
		$solicitudCredito->tercero_id = $tercero->id;
		$solicitudCredito->modalidad_credito_id = $modalidadCredito->id;
		$solicitudCredito->valor_solicitud = $credito['valor_credito'];
		$solicitudCredito->valor_credito = $credito['valor_credito'];
		$solicitudCredito->fecha_solicitud = $procesoCreditosLote->fecha_proceso;
		$solicitudCredito->quien_inicio_usuario = optional($usuario)->usuario;
		$solicitudCredito->quien_inicio = optional($usuario)->nombre_corto;
		$solicitudCredito->fecha_primer_pago = $calendario->fecha_recaudo;
		$solicitudCredito->fecha_primer_pago_intereses = $calendario->fecha_recaudo;
		$solicitudCredito->plazo = $credito['plazo'];
		$solicitudCredito->periodicidad = $pagaduria->periodicidad_pago;
		$solicitudCredito->tipo_pago_intereses = $modalidadCredito->pago_interes;
		$solicitudCredito->tipo_amortizacion = $modalidadCredito->tipo_cuota;
		$solicitudCredito->tipo_tasa = $modalidadCredito->tipo_tasa;
		$solicitudCredito->tasa = $modalidadCredito->obtenerValorTasa(
			$credito['valor_credito'],
			$credito['plazo'],
			$procesoCreditosLote->fecha_proceso,
			$tercero->socio->fecha_ingreso,
			$tercero->socio->fecha_antiguedad,
			$solicitudCredito->periodicidad
		);
		if($modalidadCredito->tipo_cuota == 'FIJA') {
			$solicitudCredito->valor_cuota = FinancieroHelper::obtenerValorCuota($credito['valor_credito'], $credito['plazo'], $modalidadCredito->tipo_cuota, $solicitudCredito->tasa, $pagaduria->periodicidad_pago);
		}
		else {
			$solicitudCredito->valor_cuota = FinancieroHelper::obtenerValorCuota($credito['valor_credito'], $credito['plazo']);
		}
		$solicitudCredito->aplica_mora = $modalidadCredito->aplica_mora;
		$solicitudCredito->tasa_mora = $modalidadCredito->tasa_mora;
		$solicitudCredito->tipo_garantia = 'PERSONAL';
		$solicitudCredito->forma_pago = 'NOMINA';
		$solicitudCredito->calificacion_obligacion = 'A';
		$solicitudCredito->estado_solicitud = 'BORRADOR';
		$solicitudCredito->canal = 'OFICINA';
		return $solicitudCredito;
	}

	private function validar($credito) {
		$errores = null;
		$validador = Validator::make($credito->all(), [
				'tercero_id'	=> 'bail|required|integer|min:1',
				'valor_credito' => 'bail|required|integer|min:1',
				'plazo'			=> 'bail|required|integer|min:1'
			],
			[
				'tercero_id.required'	=> 'La :attribute no encontrado',
				'tercero_id.integer'	=> 'La :attribute debe contener un número de identificación válido',
				'tercero_id.min'		=> 'La :attribute debe contener un número de identificación válido',
				'valor_credito.integer'	=> 'El :attribute debe contener un valor válido',
				'valor_credito.min'		=> 'El :attribute debe contener un valor válido',
				'plazo.integer'			=> 'El :attribute debe contener un valor válido',
				'plazo.min'				=> 'El :attribute debe contener un valor válido',
			],
			[
				'tercero_id'	=> 'identificación',
				'valor_credito'	=> 'valor',
				'cuotas'		=> 'cuotas'
		]);
		if($validador->fails()) {
			$errores = implode(', ', $validador->errors()->all());
		}
		return $errores;
	}

	public function desembolso(ProcesoCreditosLote $obj) {
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: Proceso en estado diferente a CARGADO');
			return redirect('procesoCreditoLote');
		}
		return view('creditos.procesoCreditoLote.desembolso')->withProceso($obj);
	}

	public function anular(ProcesoCreditosLote $obj) {
		$msg = "Ingresó a anular la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'INGRESAR');
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('procesoCreditoLote');
		}
		return view('creditos.procesoCreditoLote.confirmarAnulacion')->withProceso($obj);
	}

	public function updateAnular(ProcesoCreditosLote $obj) {
		$msg = "Anuló la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'ACTUALIZAR');
		$this->objEntidad($obj);
		if($obj->estado != 'PRECARGA' && $obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede anular un proceso en estado diferente a PRECARGA o CARGADO');
			return redirect('procesoCreditoLote');
		}
		$obj->detallesProcesoCreditoLote()->delete();
		$obj->estado = 'ANULADO';
		$obj->save();
		Session::flash('message', 'Se ha anulado el proceso ' . $obj->consecutivo_proceso);
		return redirect('procesoCreditoLote');
	}

	public function limpiar(ProcesoCreditosLote $obj) {
		$msg = "Ingresó a limpiar la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'INGRESAR');
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('procesoCreditoLote');
		}
		return view('creditos.procesoCreditoLote.confirmarLimpiar')->withProceso($obj);
	}

	public function updateLimpiar(ProcesoCreditosLote $obj) {
		$msg = "Limpió la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'ACTUALIZAR');
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede limpiar un proceso en estado diferente a CARGADO');
			return redirect('procesoCreditoLote');
		}
		$obj->detallesProcesoCreditoLote()->delete();
		$obj->estado = 'PRECARGA';
		$obj->save();
		Session::flash('message', 'Se ha limpiado la carga del proceso ' . $obj->consecutivo_proceso);
		return redirect()->route('procesoCreditoLoteCargarCreditos', $obj);
	}

	public function desembolsar(ProcesoCreditosLote $obj) {
		$msg = "Ingresó a desembolsar la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'INGRESAR');
		$this->objEntidad($obj);
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede desembolsar un proceso en estado diferente a CARGADO');
			return redirect('procesoCreditoLote');
		}
		if($this->moduloCerrado(7, $obj->fecha_proceso)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
		}
		return view('creditos.procesoCreditoLote.confirmarDesembolso')->withProceso($obj);
	}

	public function updateDesembolsar(ProcesoCreditosLote $obj) {
		$msg = "Desembolsó la solicitud de crédito en lote '%s'";
		$this->log(sprintf($msg, $obj->id), 'ACTUALIZAR');
		$this->objEntidad($obj);

		$usuario = $this->getUser();
		if($obj->estado != 'CARGADO') {
			Session::flash('error', 'Error: No se puede desembolsar un proceso en estado diferente a CARGADO');
			return redirect('procesoCreditoLote');
		}
		if($this->moduloCerrado(7, $obj->fecha_proceso)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
		}
		if(!$obj->esValido()) {
			Session::flash('error', 'Error: ' . $obj->detalle_error);
			return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
		}

		//Se inicia transaccion
		DB::beginTransaction();
		try {
			$tipoComprobante = TipoComprobante::entidadId($obj->entidad_id)->whereCodigo('DCLO')->uso('PROCESO')->first();

			$movimientoTemporal = new MovimientoTemporal;

			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = "Desembolso de créditos proceso " . $obj->consecutivo_proceso;
			$movimientoTemporal->fecha_movimiento = $obj->fecha_proceso;
			$movimientoTemporal->origen = 'PROCESO';
			$movimientoTemporal->save();

			//Se busca la cuenta de parametrización de cartera
			$cuenta = ParametroContable::entidadId($this->getEntidad()->id)->tipoCartera('CONSUMO');
			$cuenta = $cuenta->tipoGarantia('OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
			$cuenta = $cuenta->categoriaClasificacion('A')->first();

			if($cuenta == null) {
				DB::rollBack();
				Session::flash('error', 'Error: No se encontró parametrización de clasificación contable para créditos.');
				return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
			}
			$serie = 1;
			$detalles = array();

			$solicitudes = $obj->getSolicitudesCreditos();
			foreach ($solicitudes as &$solicitud) {
				$solicitud->quien_radico_usuario = optional($usuario)->usuario;
				$solicitud->quien_radico = optional($usuario)->nombre_corto;
				$solicitud->quien_aprobo_usuario = optional($usuario)->usuario;
				$solicitud->quien_aprobo = optional($usuario)->nombre_corto;
				$solicitud->quien_desembolso_usuario = optional($usuario)->usuario;
				$solicitud->quien_desembolso = optional($usuario)->nombre_corto;
				$solicitud->estado_solicitud = 'DESEMBOLSADO';
				$solicitud->fecha_aprobacion = $solicitud->fecha_solicitud;
				$solicitud->fecha_desembolso = $solicitud->fecha_solicitud;
				$solicitud->observaciones = $obj->descripcion;
				$solicitud->save();
				$respuesta = DB::select('select creditos.fn_asignacion_numero_obligacion(?, ?) as numero', [$this->getEntidad()->id, $solicitud->id]);
				if(!count($respuesta)) {
					DB::rollBack();
					Session::flash('error', 'Error: Asignando número de obligación');
					return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
				}
				$solicitud->numero_obligacion = $respuesta[0]->numero;
				$solicitud->save();
				DB::statement('exec creditos.sp_amortizacion_credito ?', [$solicitud->id]);

				//Se crean los detalles del movimiento temporal
				$ajuste = new DetalleMovimientoTemporal;

				$ajuste->entidad_id = $this->getEntidad()->id;
				$ajuste->codigo_comprobante = $tipoComprobante->codigo;
				$ajuste->tercero_id = $solicitud->tercero->id;
				$ajuste->tercero_identificacion = $solicitud->tercero->numero_identificacion;
				$ajuste->tercero = $solicitud->tercero->nombre;
				$ajuste->cuif_id = $cuenta->cuentaCapital->id;
				$ajuste->cuif_codigo = $cuenta->cuentaCapital->codigo;
				$ajuste->cuif_nombre = $cuenta->cuentaCapital->nombre;
				$ajuste->serie = $serie++;
				$ajuste->fecha_movimiento = $obj->fecha_proceso;
				$ajuste->credito = 0;
				$ajuste->debito = $solicitud->valor_credito;
				$ajuste->referencia = $solicitud->numero_obligacion;
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
			$ajusteContrapartida->credito = $obj->total_valor_creditos;
			$ajusteContrapartida->debito = 0;
			$ajusteContrapartida->referencia = $obj->referencia;
			array_push($detalles, $ajusteContrapartida);

			$movimientoTemporal->detalleMovimientos()->saveMany($detalles);

			$respuesta = DB::select('exec creditos.sp_contabilizar_desembolso_credito_lote ?, ?', [$obj->id, $movimientoTemporal->id]);
			if(!count($respuesta)) {
				DB::rollBack();
				Session::flash('error', 'Error: Contabilizando el comprobante');
				return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
			}
			if(!empty($respuesta[0]->ERROR)) {
				DB::rollBack();
				Session::flash('error', 'Error: ' . $respuesta[0]->MENSAJE);
				return redirect()->route('procesoCreditoLoteDesembolso', $obj->id);
			}
			$idComprobante = $respuesta[0]->MENSAJE;

			foreach ($solicitudes as $solicitud) {
				$movimientoCapital = new MovimientoCapitalCredito;
				$movimientoCapital->solicitud_credito_id = $solicitud->id;
				$movimientoCapital->movimiento_id = $idComprobante;
				$movimientoCapital->fecha_movimiento = $obj->fecha_proceso;
				$movimientoCapital->valor_movimiento = $solicitud->valor_credito;
				$movimientoCapital->save();
				if($this->getEntidad()->usa_tarjeta) {
					event(new CalcularAjusteAhorrosVista($solicitud->id, true));
				}
			}
			DB::commit();
			Session::flash('message', 'Se ha desembolsado el proceso de créditos ' . $obj->consecutivo_proceso);
			return redirect('procesoCreditoLote');
		}
		catch(Exception $e) {
			DB::rollBack();
		}
	}

	public static function routes() {
		Route::get('procesoCreditoLote', 'Creditos\ProcesoCreditoLoteController@index');
		Route::get('procesoCreditoLote/create', 'Creditos\ProcesoCreditoLoteController@create');
		Route::post('procesoCreditoLote', 'Creditos\ProcesoCreditoLoteController@store');
		Route::get('procesoCreditoLote/{obj}/cargarCreditos', 'Creditos\ProcesoCreditoLoteController@cargarCreditos')->name('procesoCreditoLoteCargarCreditos');
		Route::put('procesoCreditoLote/{obj}/cargarCreditos', 'Creditos\ProcesoCreditoLoteController@updateCargarCreditos')->name('procesoCreditoLoteCargarCreditosPut');
		Route::get('procesoCreditoLote/{obj}/desembolso', 'Creditos\ProcesoCreditoLoteController@desembolso')->name('procesoCreditoLoteDesembolso');
		Route::get('procesoCreditoLote/{obj}/anular', 'Creditos\ProcesoCreditoLoteController@anular')->name('procesoCreditoLoteAnular');
		Route::put('procesoCreditoLote/{obj}/anular', 'Creditos\ProcesoCreditoLoteController@updateAnular')->name('procesoCreditoLotePutAnular');
		Route::get('procesoCreditoLote/{obj}/limpiar', 'Creditos\ProcesoCreditoLoteController@limpiar')->name('procesoCreditoLoteLimpiar');
		Route::put('procesoCreditoLote/{obj}/limpiar', 'Creditos\ProcesoCreditoLoteController@updateLimpiar')->name('procesoCreditoLotePutLimpiar');
		Route::get('procesoCreditoLote/{obj}/desembolsar', 'Creditos\ProcesoCreditoLoteController@desembolsar')->name('procesoCreditoLoteDesembolsar');
		Route::put('procesoCreditoLote/{obj}/desembolsar', 'Creditos\ProcesoCreditoLoteController@updateDesembolsar')->name('procesoCreditoLotePutDesembolsar');
	}
}
