<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contabilidad\Comprobante\CreateComprobanteRequest;
use App\Http\Requests\Contabilidad\Comprobante\CreateImpuestoRequest;
use App\Http\Requests\Contabilidad\Comprobante\EditComprobanteRequest;
use App\Http\Requests\Contabilidad\Comprobante\EditDetalleRequest;
use App\Http\Requests\Contabilidad\Comprobante\UploadDetalleMovimientosRequest;
use App\Models\Contabilidad\CausaAnulacionMovimiento;
use App\Models\Contabilidad\ConceptoImpuesto;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimiento;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\Impuesto;
use App\Models\Contabilidad\Movimiento;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\Tercero;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Log;
use Route;
use Validator;
use Illuminate\Support\Str;

class ComprobanteController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$request = $request->validate([
			'name' => 'bail|nullable|string|max:40',
			'tipo' => [
				'bail',
				'nullable',
				'exists:sqlsrv.contabilidad.tipos_comprobantes,id,entidad_id,' .
				$this->getEntidad()->id . ',deleted_at,NULL'
			],
			'inicio' => 'bail|nullable|date_format:"d/m/Y"',
			'fin' => 'bail|nullable|date_format:"d/m/Y"|after_or_equal:inicio',
			'estado' => 'bail|nullable|string|in:CONTABILIZADO,SIN CONTABILIZAR,ANULADO',
			'origen' => 'bail|nullable|string|in:MANUAL,PROCESO'
		]);
		$this->logActividad("Ingreso a comprobantes", collect($request));
		$request = (object) $request;
		$tiposComprobantes = TipoComprobante::entidadId()->orderBy('codigo')
			->get()->pluck('nombre_completo', 'id');

		if (!isset($request->estado) || empty($request->estado))$request->estado = 'CONTABILIZADO';

		$comprobantes = null;
		if ($request->estado == 'SIN CONTABILIZAR') {
			$comprobantes = MovimientoTemporal::entidadId();
		}
		else {
			$comprobantes = Movimiento::entidadId();
			$comprobantes = $comprobantes->anulado($request->estado == 'CONTABILIZADO' ? false : true);
			$comprobantes = $comprobantes->origen(optional($request)->origen);
		}
		$comprobantes = $comprobantes->search(optional($request)->name);
		$comprobantes = $comprobantes->comprobante(optional($request)->tipo);
		if(!empty($request->inicio)) {
			$i = Carbon::createFromFormat('d/m/Y', $request->inicio)->startOfDay();
			$comprobantes = $comprobantes->where('fecha_movimiento', '>=', $i);
		}
		if (!empty($request->fin)) {
			$f = Carbon::createFromFormat('d/m/Y', $request->fin)->endOfDay()->subSecond();
			$comprobantes = $comprobantes->where('fecha_movimiento', '<=', $f);
		}
		$comprobantes = $comprobantes->orderBy('fecha_movimiento', 'desc')->orderBy('id', 'desc');
		$comprobantes = $comprobantes->with('tipoComprobante')->paginate();
		return view('contabilidad.comprobante.index')
			->withComprobantes($comprobantes)->withTiposComprobantes($tiposComprobantes);
	}

	public function create() {
		$this->log("Ingresó a crear comprobantes", 'INGRESAR');
		return view('contabilidad.comprobante.create');
	}

	public function store(CreateComprobanteRequest $request) {
		$msg = "Creó un comprobante temporal con los siguientes parámetros %s";
		$this->log(sprintf($msg, json_encode($request->all())), 'CREAR');
		$movimientoTemporal = MovimientoTemporal::create([
			'entidad_id' => $this->getEntidad()->id,
			'fecha_movimiento' => $request->fecha_movimiento,
			'tipo_comprobante_id' => $request->tipo_comprobante_id,
			'descripcion'=>$request->descripcion,
			'origen' => 'MANUAL'
		]);
		return redirect()->route('comprobanteEdit', $movimientoTemporal->id);
	}

	public function edit(MovimientoTemporal $obj) {
		$this->log(sprintf("Ingresó a editar el comprobante temporal %s", $obj->id), "INGRESAR");
		$this->objEntidad($obj, "No autorizado a editar el comprobante");
		$dt = DetalleMovimientoTemporal::whereMovimientoId($obj->id)->orderBy('serie', 'desc')->get();
		return view('contabilidad.comprobante.edit')->withComprobante($obj)->withDetalles($dt);
	}

	public function update(EditComprobanteRequest $request, MovimientoTemporal $obj) {
		$msg = "Ingresó a editar el comprobante temporal %s con los siguientes parámetros %s";
		$msg = sprintf($msg, $obj->id, json_encode($request->all()));
		$this->log($msg, "ACTUALIZAR");
		$this->objEntidad($obj, "No autorizado a editar el comprobante");
		$obj->descripcion = $request->descripcion;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el comprobante');
		return redirect()->route('comprobanteEdit', $obj->id);
	}

	public function updateDetalle(EditDetalleRequest $request, MovimientoTemporal $obj) {
		$this->objEntidad($obj);
		$tercero = Tercero::find($request->tercero);
		$cuenta = Cuif::find($request->cuenta);

		$dt = new DetalleMovimientoTemporal;

		$dt->codigo_comprobante = $obj->tipoComprobante->codigo;
		$dt->entidad_id = $this->getEntidad()->id;
		$dt->setTercero($tercero);
		$dt->setCuif($cuenta);
		$dt->debito = empty($request->debito) ? 0 : $request->debito;
		$dt->credito = empty($request->credito) ? 0 : $request->credito;
		$dt->referencia = $request->referencia;
		$dt->serie = $obj->detalleMovimientos->count() + 1;
		$dt->fecha_movimiento = $obj->fecha_movimiento;

		$obj->detalleMovimientos()->save($dt);

		$d = $dt->movimiento->debitos;
		$c = $dt->movimiento->creditos;
		$diff = $d - $c;
		$resultado = array(
			'debitos' => number_format($d, 0),
			'creditos' => number_format($c, 0),
			'diferencia' => number_format($diff, 0),
			'registros' => $dt->movimiento->detalleMovimientos->count()
		);
		$resultado['item'] = array(
			'id' => $dt->id,
			'cuenta' => Str::limit($dt->cuenta->full, 30),
			'tercero' => Str::limit($dt->terceroRelacion->nombre_completo, 30),
			'referencia' => Str::limit($dt->referencia, 30),
			'debito' => number_format($dt->debito, 0),
			'credito' => number_format($dt->credito, 0)
		);
		return response()->json($resultado);
	}

	public function deleteDetalle(Request $request, MovimientoTemporal $obj) {
		$dt = DetalleMovimientoTemporal::find($request->id);
		if(!$dt) {
			return response(['credito' => ['Error, no se encontró el registro a eliminar']], 422)
				->header('Content-Type', 'application/json');
		}

		if($dt->movimiento != $obj) {
			return response(['credito' => ['Error, el registro no pertenece al comprobante']], 422)
				->header('Content-Type', 'application/json');
		}
		$dt->delete();

		DetalleMovimientoTemporal::orderBy('serie')->chunk(1000, function ($detalles) {
			$contador = 1;
			foreach ($detalles as $detalle)$detalle->update(['serie'=> $contador++]);
		});

		$resultado = array(
			'debitos' => number_format($obj->debitos, 0),
			'creditos' => number_format($obj->creditos, 0),
			'diferencia' => number_format($obj->debitos - $obj->creditos, 0),
			'registros' => $obj->detalleMovimientos->count(),
		);
		return response()->json($resultado);
	}

	public function getDelete(MovimientoTemporal $obj)  {
		$msg = "Ingresó a eliminar el comprobante temporal %s";
		$this->log(sprintf($msg, $obj->id), "INGRESAR");
		$this->objEntidad($obj, "No autorizado a eliminar el comprobante");
		return view('contabilidad.comprobante.delete')->withComprobante($obj);
	}

	public function delete(MovimientoTemporal $obj) {
		$this->log("Eliminó el comprobante temporal " . $obj->id, "ELIMINAR");
		$this->objEntidad($obj, "No autorizado a eliminar el comprobante");
		$obj->delete();
		Session::flash('message', 'Se ha eliminado el comprobante');
		return redirect('comprobante');
	}

	public function referencia(Request $request) {
		$validator = Validator::make($request->all(), [
			'term'  => 'bail|string|min:2|max:1000',
		]);
		return response()->json([]);
		if($validator->fails())return response()->json([]);
		/*$referencias = DB::table('contabilidad.v_referencias')
			->where('entidad_id', $this->getEntidad()->id)
			->where('referencia', 'like', '%' . $request->term  . '%')
			->orderBy('referencia')
			->take(20)
			->pluck('referencia');*/

		$data = collect([]);
		foreach($referencias as $det)$data->push(collect(['label' => $det, 'value' => $det]));
		return response()->json($data);
	}

	public function getContabilizar(MovimientoTemporal $obj) {
		$msg = "Ingresó a contabilizar el comprobante temporal %s";
		$this->log(sprintf($msg, $obj->id), "INGRESAR");
		$this->objEntidad($obj, "No autorizado a contabilizar el comprobante");
		return view('contabilidad.comprobante.contabilizar')->withComprobante($obj);
	}

	public function contabilizar(MovimientoTemporal $obj) {
		$msg = "Ingresó a contabilizar el comprobante temporal %s";
		$this->log(sprintf($msg, $obj->id), "ACTUALIZAR");
		$this->objEntidad($obj, "No autorizado a contabilizar el comprobante");
		if($this->moduloCerrado(2, $obj->fecha_movimiento)) {
			Session::flash("error","Módulo de contabilidad cerrado para la fecha de movimiento.");
			return redirect()->route("comprobanteEdit", $obj->id);
		}
		$respuesta = DB::select('exec contabilidad.sp_contabilizar ?', [$obj->id]);
		if (!$respuesta) {
			Session::flash('faltantes', ['error' => "Error contabilizando el comprobante, ya se ha contabilizado"]);
			return redirect('comprobante');
		}
		if($respuesta[0]->ERROR == 1) {
			Session::flash('faltantes', ['error' => $respuesta[0]->MENSAJE]);
			return redirect()->route('comprobanteEdit', $obj);
		}
		Session::flash('message', $respuesta[0]->MENSAJE);
		return redirect('comprobante');
	}

	/**
	 * Muestra el formulario para cargar plano con movimientos
	 * y los posibles errores que se puedan generar
	 * @param MovimientoTemporal $obj
	 * @return type
	 */
	public function getCargue(MovimientoTemporal $obj) {
		$this->log("Ingresó al formulario de cargue de plano para movimientos contables, " . $obj->id);
		$this->objEntidad($obj, "No autorizado a cargar plano contable");
		$resumen = null;
		if (Session::has('resumen'))$resumen = Session::get('resumen')[0];
		return view('contabilidad.comprobante.cargue')->withComprobante($obj)->withResumen($resumen);
	}

	public function cargue(MovimientoTemporal $obj,UploadDetalleMovimientosRequest $request) {
		$this->log(
			"Cargó archivo plano de movimientos contables para el " .
			"comprobante temporal $obj->id con los siguientes parámetros " .
			json_encode($request->all()), "ACTUALIZAR"
		);
		$this->objEntidad($obj, "No autorizado a cargar plano contable");
		$contenido = $request->file('archivo')->openFile();
		$contenido = $contenido->fread($contenido->getSize());
		$contenido = explode("\n", $contenido);
		if (count($contenido) <= 1) {
			Session::flash('error', 'Error: archivo se encuentra vacio');
			return redirect()->route('comprobante.cargue', ['obj' => $obj->id]);
		}
		$datosMovimiento = array();
		$errores = collect();
		$fila = 0;
		$cantidadErrores = 0;
		$cantidadCorrectos = 0;
		$debitos = 0;
		$creditos = 0;
		foreach ($contenido as $item) {
			if($fila == 0) {
				$fila++;
				continue;
			}
			$item = str_ireplace("\r", "", $item);
			$movimiento = explode(";", $item);
			if (count($movimiento) != 5) {
				$errores->push('El registro ' . $fila . ' no cumple con la estructura.');
				$cantidadErrores++;
				$fila++;
				continue;
			}
			$movimiento = collect([
				'cuif_codigo' => trim($movimiento[0]),
				'tercero_identificacion' => trim($movimiento[1]),
				'referencia' => trim($movimiento[2]),
				'debito' => trim($movimiento[3]),
				'credito' => trim($movimiento[4])
			]);
			$resultadoValidacion = $this->validar($movimiento);
			if($resultadoValidacion != null) {
				$errores->push('Registro ' . $fila . ': ' . $resultadoValidacion);
				$fila++;
				$cantidadErrores++;
				continue;
			}
			$movimiento = $this->construirDetalleMovimiento($movimiento, $obj);
			if(!($movimiento instanceof DetalleMovimientoTemporal)) {
				$errores->push('Registro ' . $fila . ': ' . $movimiento);
				$fila++;
				$cantidadErrores++;
				continue;
			}
			$debitos += intval($movimiento->debito);
			$creditos += intval($movimiento->credito);
			$datosMovimiento[] = $movimiento;
			$cantidadCorrectos++;
			$fila++;
		}
		$resumen = array([
			'registros' => $fila,
			'cantidadErrores' => $cantidadErrores,
			'cantidadCorrectos' => $cantidadCorrectos,
			'errores' => $errores,
			'debitos' => $debitos,
			'creditos' => $creditos
		]);
		if ($cantidadCorrectos > 0)$obj->detalleMovimientos()->saveMany($datosMovimiento);
		Session::flash('resumen', $resumen);
		return redirect()->route('comprobante.cargue', ['obj' => $obj->id]);
	}

	private function construirDetalleMovimiento($movimiento, $movimientoTemporal) {
		//Se busca la cuenta contable
		$cuenta = Cuif::entidadId()->activa()->whereCodigo($movimiento["cuif_codigo"])->first();
		if (!$cuenta)return "Cuenta no existe";
		if ($cuenta->modulo_id != 1 && $cuenta->modulo_id != 2)return "Uso de cuenta auxiliar restringido";

		//Se busca el tercero
		$tercero = Tercero::activo()->entidadTercero()
			->whereNumeroIdentificacion($movimiento["tercero_identificacion"])->first();
		if (!$tercero)return "Tercero no existe";

		$movimiento->put("entidad_id", $this->getEntidad()->id);
		$movimiento->put("tercero_id", $tercero->id);
		$movimiento->put("tercero", $tercero->nombre);
		$movimiento->put("codigo_comprobante", $movimientoTemporal->tipoComprobante->codigo);
		$movimiento->put("cuif_id", $cuenta->id);
		$movimiento->put("cuif_nombre", $cuenta->nombre);
		$movimiento->put("serie", 1);
		$movimiento->put("fecha_movimiento", $movimientoTemporal->fecha_movimiento);
		$detalleMovimientoTemporal = new DetalleMovimientoTemporal;
		$detalleMovimientoTemporal->fill($movimiento->toArray());
		return $detalleMovimientoTemporal;
	}

	private function validar($movimiento) {
		$entidad = $this->getEntidad();
		$errores = null;
		$validador = Validator::make($movimiento->all(), [
			'cuif_codigo' => [
				'bail',
				'required',
				'string',
				'exists:sqlsrv.contabilidad.cuifs,codigo,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL',
			],
			'tercero_identificacion' => [
				'bail',
				'required',
				'string',
				'exists:sqlsrv.general.terceros,numero_identificacion,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
			],
			'referencia' => 'bail|nullable|regex:/^[a-zA-Z0-9-+*\/.,;:]*$/',
			'debito' => 'bail|required|integer',
			'credito' => 'bail|required|integer',
		],
		[
			'cuif_codigo.required' => 'La :attribute es requerida',
			'cuif_codigo.exists' => 'Cuenta auxiliar no existente',
			'tercero_identificacion.required' => 'El :attribute es requerido',
			'tercero_identificacion.exists' => 'Tercero no encontrado',
			'referencia.regex' => 'La :attribute contiene elementos invalidos, solo (letras, números y (-+*/.,;:) sin espacios)',
			'debito.required' => 'El :attribute es requerido',
			'credito.required' => 'El :attribute es requerido',
			'debito.integer' => 'El :attribute debe ser un número entero',
			'credito.integer' => 'El :attribute debe ser un número entero',
		],
		[
			'cuif_codigo' => 'cuenta',
			'tercero_identificacion' => 'tercero',
			'debito' => 'débito',
			'credito' => 'crédito',
		]);
		if($validador->fails()) {
			$errores = implode(', ', $validador->errors()->all());
		}
		$debito = intval($movimiento["debito"]);
		$credito = intval($movimiento["credito"]);
		if($debito == 0 && $credito == 0) {
			$errores .= ($errores ? ", ": "") . "Se requiere un valor en débito o crédito";
		}
		if($debito != 0 && $credito != 0) {
			$errores .= ($errores ? ", ": "") . "Sólo se admite un valor en débito o crédito";
		}
		return $errores;
	}

	public function duplicar(Movimiento $obj) {
		$this->objentidad($obj);
		if ($obj->origen != 'MANUAL') {
			Session::flash("error", "El comprobante no se de origen manual");
			return redirect('comprobante');
		}
		$comprobante = $obj->tipoComprobante->codigo . $obj->numero_comprobante;
		$mensajeLog = sprintf("Ingreso a duplicar el comprobante %s (%s)", $comprobante, $obj->id);
		$this->log($mensajeLog);
		return view('contabilidad.comprobante.duplicar')->withComprobante($obj);
	}

	public function duplicarComprobante(Movimiento $obj, CreateComprobanteRequest $request) {
		$this->objentidad($obj);
		if ($obj->origen != 'MANUAL') {
			Session::flash("error", "El comprobante no se de origen manual");
			return redirect('comprobante');
		}
		$mensajeLog = "Duplicó el comprobante %s (%s) con los siguientes parámetros %s";
		$comprobante = $obj->tipoComprobante->codigo . $obj->numero_comprobante;
		$mensajeLog = sprintf($mensajeLog, $comprobante, $obj->id, json_encode($request->all()));
		$this->log($mensajeLog, "ACTUALIZAR");

		$entidad = $this->getEntidad();
		try {
			DB::beginTransaction();
			$mt = MovimientoTemporal::create([
				'entidad_id' => $entidad->id,
				'fecha_movimiento' => $request->fecha_movimiento,
				'tipo_comprobante_id' => $request->tipo_comprobante_id,
				'descripcion' => $request->descripcion,
				'origen' => 'MANUAL'
			]);
			$detalles = collect();
			$detallesMovimientos = $obj->detalleMovimientos()->orderBy('serie')->orderBy('id')->get();
			$serie = 1;
			foreach ($detallesMovimientos as $detalle) {
				$det = new DetalleMovimientoTemporal;
				$det->codigo_comprobante = $mt->tipoComprobante->codigo;
				$det->entidad_id = $entidad->id;
				$det->tercero_id = $detalle->tercero_id;
				$det->tercero_identificacion = $detalle->tercero_identificacion;
				$det->tercero = $detalle->tercero;
				$det->cuif_id = $detalle->cuif_id;
				$det->cuif_codigo = $detalle->cuif_codigo;
				$det->cuif_nombre = $detalle->cuif_nombre;
				$det->debito = $detalle->debito;
				$det->credito = $detalle->credito;
				$det->serie = $serie++;
				$det->referencia = $detalle->referencia;
				$det->fecha_movimiento = $mt->fecha_movimiento;
				$detalles->push($det);
			}
			$mt->detalleMovimientos()->saveMany($detalles->all());
			DB::commit();
			Session::flash("message", "Se ha duplicado el comprobante");
			return redirect()->route('comprobanteEdit', $mt->id);
		} catch(Exception $e) {
			DB::rollBack();
			$mensaje = sprintf("Error duplicando el comprobante %s: %s", $obj->id, $e->getMessage());
			Log::error($mensaje);
			Session::flash("error", "Error duplicando el comprobante");
			return redirect()->route('comprobante.duplicar', $obj->id);
		}
	}

	public function anular(Movimiento $obj) {
		$this->objentidad($obj);
		if ($obj->origen != 'MANUAL') {
			Session::flash("error", "El comprobante no se de origen manual");
			return redirect('comprobante');
		}
		$mensajeLog = "Ingreso a anular el comprobante %s (%s)";
		$comprobante = $obj->tipoComprobante->codigo . $obj->numero_comprobante;
		$mensajeLog = sprintf($mensajeLog, $comprobante, $obj->id);
		$this->log($mensajeLog);

		$causasAnulacion = CausaAnulacionMovimiento::entidadId()->activa()->pluck('nombre', 'id');

		return view('contabilidad.comprobante.anular')->withComprobante($obj)
			->withCausasAnulacion($causasAnulacion);
	}

	public function anularComprobante(Movimiento $obj, Request $request) {
		$this->objentidad($obj);
		$this->validarParaAnular($obj, $request);

		$mensajeLog = "Anuloó el comprobante %s (%s) con los siguientes parámetros %s";
		$comprobante = $obj->tipoComprobante->codigo . $obj->numero_comprobante;
		$mensajeLog = sprintf($mensajeLog, $comprobante, $obj->id, json_encode($request->all()));
		$this->log($mensajeLog, "ACTUALIZAR");

		$entidad = $this->getEntidad();
		try {
			DB::beginTransaction();
			$obj->causa_anulado_id = $request->causa_anulacion_id;
			$usuario = $this->getUser();
			$obj->anulado_por_usuario = $usuario->usuario;
			$obj->anulado_por_nombre = $usuario->nombreCompleto;
			$obj->save();
			DB::commit();
			Session::flash("message", "Se ha anulado el comprobante");
			return redirect('comprobante');
		} catch(Exception $e) {
			DB::rollBack();
			$mensaje = sprintf("Error anulando el comprobante %s: %s", $obj->id, $e->getMessage());
			Log::error($mensaje);
			Session::flash("error", "Error anulando el comprobante");
			return redirect()->route('comprobante.anular', $obj->id);
		}
	}

	private function validarParaAnular($obj, $request) {
		$entidad = $this->getEntidad();
		$fechaMovimiento = $obj->fecha_movimiento->format("d/m/Y");
		$tipo = $obj->tipoComprobante;
		$cuentasValidas = $this->validarCuentasParaAnular($obj);
		$request->request->add([
			"fecha_movimiento" => $fechaMovimiento,
			"origen" => $obj->origen,
			"tipo" => "$tipo->uso"
		]);
		if (!$cuentasValidas) {
			$request->request->add(["cuentas" => "false"]);
		}
		$reglas = [
			'causa_anulacion_id' => [
				'bail',
				'required',
				'exists:sqlsrv.contabilidad.causas_anulacion_movimiento,id,' .
				'entidad_id,' . $entidad->id . ',deleted_at,NULL',
			],
			'fecha_movimiento' => [
				'bail',
				'required',
				'date_format:"d/m/Y"',
				'modulocerrado:2'
			],
			'origen' => 'bail|required|string|in:MANUAL',
			'tipo' => 'bail|required|string|in:MANUAL',
			'cuentas' => 'bail|nullable|string|in:true'
		];
		$mensajes = [
			'causa_anulacion_id.request' => 'La :attribute es requerido.',
			'origen.in' => 'El comprobante debe ser de origen MANUAL.',
			'fecha_movimiento.modulocerrado' => 'Módulo de contabilidad cerrado.',
			'tipo.in' => 'El :attribute no permitido, solo es permitido el tipo de comprobante MANUAL',
			'cuentas.in' => 'Registros con cuentas contables no válidas para anulación.'
		];
		$atributos = ['causa_anulacion_id' => 'Causa anulación', 'tipo' => 'tipo comprobante'];
		$request->validate($reglas, $mensajes, $atributos);
	}

	public function validarCuentasParaAnular($obj) {
		$registros = $obj->detalleMovimientos()->with(["cuenta", "cuenta.modulo"])->get();
		foreach($registros as $registro) {
			$modulo = $registro->cuenta->modulo;
			if($modulo->id != 1 && $modulo->id != 2 && $modulo->id != 5) {
				return false;
			}
		}
		return true;
	}

	public function impuestos(MovimientoTemporal $obj) {
		$this->log(sprintf("Ingresó a agregar impuestos para el comprobante temporal %s", $obj->id));
		$i = Impuesto::entidadId()->with("conceptosImpuestos")->activo()->get();
		return view('contabilidad.comprobante.impuesto')->withMovimiento($obj)->withImpuestos($i);
	}

	public function crearImpuestos(MovimientoTemporal $obj, CreateImpuestoRequest $request) {
		$this->objEntidad($obj);
		$e = $this->getEntidad();
		$t = Tercero::find($request->tercero);
		$c = ConceptoImpuesto::find($request->concepto);
		$i = $c->impuesto;
		$cuif = $c->cuentaDestino;
		$impuesto = intval($request->base * ($c->tasa / 100));

		$mit = new MovimientoImpuestoTemporal;
		$mit->entidad_id = $e->id;
        $mit->movimiento_termporal_id = $obj->id;
        $mit->setTercero($t);
        $mit->fecha_movimiento = $obj->fecha_movimiento->format('d/m/Y');
        $mit->impuesto_id = $request->impuesto;
        $mit->concepto_impuesto_id = $request->concepto;
        $mit->setCuif($cuif);
        $mit->base = $request->base;
        $mit->tasa = $c->tasa;
        $mit->iva = $request->iva;

        $dmt = new DetalleMovimientoTemporal;
        $dmt->entidad_id = $e->id;
		$dmt->codigo_comprobante = $obj->tipoComprobante->codigo;
		$dmt->movimiento_id = $obj->id;
		$dmt->setTercero($t);
		$dmt->setCuif($cuif);
		$dmt->debito = $impuesto > 0 ? 0 : abs($impuesto);
		$dmt->credito = $impuesto > 0 ? abs($impuesto) : 0;
		$dmt->serie = $obj->detalleMovimientos->count() + 1;
		$dmt->fecha_movimiento = $obj->fecha_movimiento->format('d/m/Y');
		$dmt->referencia = "";

		try {
			DB::beginTransaction();
			$dmt->save();
			$mit->detalle_movimientos_temporal_id = $dmt->id;
			$mit->save();
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Error al agregar impuesto al comprobante: ' . $e->getMessage());
		}
		Session::flash('message', 'Impuesto agregado');
		return redirect()->route('comprobante.impuesto', $obj->id);
	}

	public function eliminarImpuestos(MovimientoTemporal $obj, MovimientoImpuestoTemporal $impuesto) {
		$this->objEntidad($obj);
		if($impuesto->movimiento_termporal_id != $obj->id) {
			return response()->json("Impuesto no pertenece al movimiento", 422);
		}
		try {
			DB::beginTransaction();
			$impuesto->detalleMovimientoTemporal->delete();
			$impuesto->delete();
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json("Error, no pudó eliminar el impuesto", 422);
		}
		return response()->json(["ok" => true]);
	}

	public function pagoImpuesto(MovimientoTemporal $obj, Request $request) {
		$this->objEntidad($obj);
		$mensaje = "Ingresó a pago de impuestos para el comprobante temporal '%s'";
		$this->log(sprintf($mensaje, $obj->id));
		$i = Impuesto::entidadId()->orderBy('nombre')->pluck('nombre', 'id');

		$e = $this->getEntidad();
		$req = $request->validate([
			'impuesto' => [
				'bail',
				'integer',
				'exists:sqlsrv.contabilidad.impuestos,id,entidad_id,' . $e->id . ',deleted_at,NULL'
			],
			'fechaCorte' => 'bail|date_format:"d/m/Y"',
		]);
		$total = 0;
		$totalRegistros = 0;
		$cuentas = collect();
		$infPagImp = null;
		$terceros = collect();
		if(!empty($req["impuesto"]) && !empty($req["fechaCorte"])) {
			$fc = Carbon::createFromFormat('d/m/Y', $req["fechaCorte"])->startOfDay();
			$sp = "EXEC contabilidad.sp_informacion_pago_impuestos ?, ?, ?";
			$infPagImp = DB::select($sp, [$e->id, $req["impuesto"], $fc]);
			if(!empty($infPagImp)) {
				foreach($infPagImp as &$imp) {
					$subTotal = floatval($imp->sub_total);
					$imp->subTotal = $subTotal;
					if(!$cuentas->has($imp->cuenta)) {
						$cuenta = (object)array(
								"cuenta" => $imp->cuenta,
								"nombre" => $imp->nombrecuenta,
								"subTotal" => $subTotal
						);
						$cuentas->put($imp->cuenta, $cuenta);
					}else {
						$cuentas[$imp->cuenta]->subTotal += $subTotal;
					}
					$total += $subTotal;
					$totalRegistros++;

					if(!$terceros->has($imp->cuenta)) {
						$terceros->put($imp->cuenta, collect());
					}

					if(!$terceros[$imp->cuenta]->has($imp->identificacion)) {
						$tercero = (object)array(
							"identificacion" => $imp->identificacion,
							"nombre" => $imp->nombre,
							"nombreCompleto" => $imp->identificacion . " " . $imp->nombre,
							"total" => $subTotal
						);
						$terceros[$imp->cuenta]->put($imp->identificacion, $tercero);
					}else {
						$terceros[$imp->cuenta][$imp->identificacion]->total += $subTotal;
					}
				}
			}
		}
		return view("contabilidad.comprobante.pagoImpuestos")
			->withMovimientoTemporal($obj)
			->withImpuestos($i)
			->withInfPagImp($infPagImp)
			->withCuentas($cuentas)
			->withTotalRegistros($totalRegistros)
			->withTotal($total)
			->withReq(count($req) ? true : false)
			->withTerceros($terceros);
	}

	public function cargarImpuestos(MovimientoTemporal $obj, Request $request) {
		$this->objEntidad($obj);
		$mensaje = "Cargó a pago de impuestos para el comprobante temporal '%s'";
		$this->log(sprintf($mensaje, $obj->id));

		$e = $this->getEntidad();
		$request->validate([
			'impuesto' => [
				'bail',
				'integer',
				'exists:sqlsrv.contabilidad.impuestos,id,entidad_id,' . $e->id . ',deleted_at,NULL'
			],
			'fechaCorte' => 'bail|date_format:"d/m/Y"',
		]);

		$fc = Carbon::createFromFormat('d/m/Y', $request["fechaCorte"])->startOfDay();
		$sp = "EXEC contabilidad.sp_informacion_pago_impuestos ?, ?, ?";
		$infPagImp = DB::select($sp, [$e->id, $request["impuesto"], $fc]);
		$data = collect();
		$cuentas = collect();
		$terceros = collect();
		if(!empty($infPagImp)) {
			foreach($infPagImp as &$imp) {
				$debito = floatval($imp->sub_total);

				if(!$data->has($imp->cuenta)) {
					$data->put($imp->cuenta, collect());
					$c = Cuif::find($imp->cuenta_id);
					$cuentas->put($imp->cuenta, $c);
				}
				if(!$terceros->has($imp->tercero_id)) {
					$t = Tercero::find($imp->tercero_id);
					$terceros->put($imp->tercero_id, $t);
				}
				if(!$data[$imp->cuenta]->has($imp->identificacion)) {
					$tercero = (object)array(
						"tercero" => $terceros->get($imp->tercero_id),
						"cuenta" => $cuentas->get($imp->cuenta),
						"debito" => $debito
					);
					$data[$imp->cuenta]->put($imp->identificacion, $tercero);
				}else {
					$data[$imp->cuenta]->$imp->identificacion->total += $subTotal;
				}
			}
			$detalles = collect();
			$codigoComprobante = $obj->tipoComprobante->codigo;
			foreach ($data as $d) {
				foreach($d as $t) {
					$det = new DetalleMovimientoTemporal;
					$det->entidad_id = $e->id;
					$det->codigo_comprobante = $codigoComprobante;
					$det->setTercero($t->tercero);
					$det->setCuif($t->cuenta);
					$det->debito = $t->debito;
					$det->credito = 0;
					$det->serie = 0;
					$det->fecha_movimiento = $obj->fecha_movimiento;
					$detalles->push($det);
				}
			}
			try {
				DB::beginTransaction(); //Incluir DB
				$obj->detalleMovimientos()->saveMany($detalles->all());
				DB::commit();
				Session::flash('message', 'Se han guardado con exito los datos de impuestos.');
			} catch(Exception $e) {
				DB::rollBack();
				Session::flash("error", "Error al guardar datos de impuestos.");
				$mensaje = 'Error guardando datos de impuestos para el comprobante temporal: %s, %s';
				Log::error(sprintf($mensaje, $obj->id, $e->getMessage()));
			}
		}else {
			Session::flash("error", "No hay datos de impuestos.");
		}
		return redirect()->route("comprobanteEdit", $obj->id);
	}

	public static function routes() {
		$c = 'Contabilidad\ComprobanteController@';
		Route::get('comprobante', $c . 'index');
		Route::get('comprobante/create', $c . 'create');
		Route::post('comprobante', $c . 'store');
		Route::get('comprobante/{obj}/edit', $c . 'edit')->name('comprobanteEdit');
		Route::put('comprobante/{obj}', $c . 'update');
		Route::get('comprobante/{obj}/delete', $c . 'getDelete')->name('comprobanteDelete');
		Route::delete('comprobante/{obj}/delete', $c . 'delete');
		Route::get('comprobante/{obj}/contabilizar', $c . 'getContabilizar')->name('comprobanteContabilizar');
		Route::post('comprobante/{obj}/contabilizar', $c . 'contabilizar');
		Route::put('comprobante/{obj}/detalle', $c . 'updateDetalle')->name('comprobanteEditDetalle');
		Route::delete('comprobante/{obj}/detalle', $c . 'deleteDetalle')->name('comprobanteDeleteDetalle');
		Route::get('comprobante/{obj}/referencia', $c . 'referencia');
		Route::get('comprobante/{obj}/cargue', $c . 'getCargue')->name('comprobante.cargue');
		Route::put('comprobante/{obj}/cargue', $c . 'cargue');
		Route::get('comprobante/{obj}/duplicar', $c . 'duplicar')->name('comprobante.duplicar');
		Route::post('comprobante/{obj}/duplicar', $c . 'duplicarComprobante');
		Route::get('comprobante/{obj}/anular', $c . 'anular')->name('comprobante.anular');
		Route::post('comprobante/{obj}/anular', $c . 'anularComprobante');
		Route::get('comprobante/{obj}/impuestos',$c . 'impuestos')->name('comprobante.impuesto');
		Route::post('comprobante/{obj}/impuesto',$c . 'crearImpuestos');
		Route::delete('comprobante/{obj}/{impuesto}',$c . 'eliminarImpuestos');
		Route::get('comprobante/{obj}/pagoImpuesto',$c . 'pagoImpuesto')->name('comprobante.pagoImpuesto');
		Route::put('comprobante/{obj}/cargarImpuestos',$c . 'cargarImpuestos')->name('comprobante.cargarImpuesto');
	}
}
