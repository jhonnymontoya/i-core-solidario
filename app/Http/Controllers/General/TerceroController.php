<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Tercero\CreateTerceroRequest;
use App\Http\Requests\General\Tercero\EditTerceroRequest;
use App\Models\General\Contacto;
use App\Models\General\Sexo;
use App\Models\General\Tercero;
use App\Models\General\TipoIdentificacion;
use App\Traits\FonadminTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;
use Route;
use Validator;

class TerceroController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin')->except(['getTerceroActivo']);
		$this->middleware('verEnt')->except(['getTerceroActivo']);
		$this->middleware('verMenu')->except(['dv', 'getTerceroActivo', 'getTerceroConParametros']);
	}

	/**
	 * Punto de entrada al fomulario de terceros
	 * @return type
	 */
	public function index(Request $request) {
		$this->logActividad("Ingreso a terceros", $request);
		$request->validate([
			'name'					=> 'bail|nullable|string|min:1|max:200',
			'naturaleza'			=> 'bail|nullable|string|in:NATURAL,JURIDICA',
			'tipoIdentificacion'	=> [
										'bail',
										'nullable',
										'integer',
										'exists:sqlsrv.general.tipos_identificacion,id,esta_activo,1,deleted_at,NULL',
									],
			'estado'				=> 'bail|nullable|boolean'
		]);
		$terceros = Tercero::entidadTercero()
								->search($request->name)
								->tipoIdentificacionId($request->tipoIdentificacion)
								->tipoTercero($request->naturaleza)
								->orderBy('nombre');
		if($request->estado != null)$terceros = $terceros->activo($request->estado);
		$terceros = $terceros->paginate();
		$tipos = TipoIdentificacion::activo()->orderBy('codigo')->select('id', 'codigo', 'nombre')->get();
		$tiposIdentificacion = array();
		foreach($tipos as $tipo)$tiposIdentificacion[$tipo->id] = $tipo->codigo . ' - ' . $tipo->nombre;
		return view('general.terceros.index')->withTerceros($terceros)->withTiposIdentificaciones($tiposIdentificacion);
	}

	/**
	 * Punto de entrada para crear terceros
	 * @return type
	 */
	public function create() {
		$this->log("Ingreso a crear tercero");
		//Tipos de identificación para personas naturales
		$tipo = TipoIdentificacion::activo()->aplicacion('NATURAL')->orderBy('codigo')->get();
		$tiposIdentificacionNatural = array();
		foreach($tipo as $value)$tiposIdentificacionNatural[$value->id] = $value->codigo . ' - ' . $value->nombre;

		//Tipos de identificación para personas jurídicas
		$tipo = TipoIdentificacion::activo()->aplicacion('JURIDICA')->orderBy('codigo')->get();
		$tiposIdentificacionJuridica = array();
		foreach($tipo as $value)$tiposIdentificacionJuridica[$value->id] = $value->codigo . ' - ' . $value->nombre;

		return view('general.terceros.create')->withNatural($tiposIdentificacionNatural)->withJuridico($tiposIdentificacionJuridica);
	}

	public function store(CreateTerceroRequest $request) {
		$this->log("Creó tercero con los siguientes parámetros " . json_encode($request->all()), 'CREAR');
		$tercero = new Tercero;

		$tercero->entidad_id = $this->getEntidad()->id;
		$tercero->tipo_tercero = $request->tipo_tercero;
		if($request->tipo_tercero == 'NATURAL') {
			//tercero natural
			$tercero->tipo_identificacion_id = $request->nTipoIdentificacion;
			$tercero->numero_identificacion = $request->nNumeroIdentificacion;
			$tercero->primer_nombre = $request->nPrimerNombre;
			$tercero->segundo_nombre = $request->nSegundoNombre;
			$tercero->primer_apellido = $request->nPrimerApellido;
			$tercero->segundo_apellido = $request->nSegundoApellido;
		}
		else {
			//tercero jurídico
			$tercero->tipo_identificacion_id = $request->jTipoIdentificacion;
			$tercero->numero_identificacion = $request->jNumeroIdentificacion;
			$tercero->razon_social = $request->jRazonSocial;
			$tercero->sigla = $request->jSigla;
		}

		$tercero->save();
		Session::flash('message', 'Se ha creado el tercero \''. $tercero->nombre_corto . '\'');
		return redirect()->route('terceroEdit', $tercero->id);

	}

	/**
	 * Muestra el formulario de edición de tercero según el tipo de tercero
	 * @param Tercero $obj 
	 * @return view
	 */
	public function edit(Tercero $obj) {
		$this->log("Ingresó a editar el tercero con id " . $obj->id);
		$this->objEntidad($obj, 'No está autorizado a editar el tercero');

		$tiposIdentificacion = array();
		$contacto = $obj->getContacto(true, true);
		$sexos = Sexo::pluck('nombre', 'id');
		$vista = "";
		if($obj->tipo_tercero == 'NATURAL') {
			$tipo = TipoIdentificacion::activo()->aplicacion('NATURAL')->orderBy('codigo')->get();
			foreach($tipo as $value)$tiposIdentificacion[$value->id] = $value->codigo . ' - ' . $value->nombre;
			$vista = "general.terceros.editNatural";
		}
		else {
			$tipo = TipoIdentificacion::activo()->aplicacion('JURIDICA')->orderBy('codigo')->get();
			foreach($tipo as $value)$tiposIdentificacion[$value->id] = $value->codigo . ' - ' . $value->nombre;
			$vista = "general.terceros.editJuridico";
		}
		return view($vista)
				->withTercero($obj)
				->withContacto($contacto)
				->withTiposIdentificacion($tiposIdentificacion)
				->withSexos($sexos);
	}

	public function update(Tercero $obj, EditTerceroRequest $request) {
		$this->log("Editó el tercero con id " . $obj->id, 'ACTUALIZAR');
		$this->objEntidad($obj, 'No está autorizado a editar el tercero');

		$obj->esta_activo = $request->esta_activo;
		$obj->tipo_identificacion_id = $request->tipo_identificacion_id;
		$obj->numero_identificacion = $request->numero_identificacion;
		$obj->actividad_economica_id = $request->actividad_economica_id;
		
		if($obj->tipo_tercero == 'NATURAL') {
			$obj->primer_nombre = $request->primer_nombre;
			$obj->segundo_nombre = $request->segundo_nombre;
			$obj->primer_apellido = $request->primer_apellido;
			$obj->segundo_apellido = $request->segundo_apellido;
			$obj->sexo_id = $request->sexo_id;
			$obj->fecha_nacimiento = $request->fecha_nacimiento;
			$obj->ciudad_nacimiento_id = $request->ciudad_nacimiento_id;
			$obj->fecha_expedicion_documento_identidad = $request->fecha_expedicion_documento_identidad;
			$obj->ciudad_expedicion_documento_id = $request->ciudad_expedicion_documento_id;
		}
		else{
			$obj->razon_social = $request->razon_social;
			$obj->sigla = $request->sigla;
			$obj->fecha_constitucion = $request->fecha_constitucion;
			$obj->ciudad_constitucion_id = $request->ciudad_constitucion_id;
			$obj->numero_matricula = $request->numero_matricula;
			$obj->matricula_renovada = $request->matricula_renovada;
		}

		try {
			DB::beginTransaction();

			$obj->save();

			if(!($obj->es_asociado || $obj->es_empleado)) {
				$contactoAnterior = $obj->getContacto(true, true);
				$contacto = new Contacto;
				if(!empty($contactoAnterior)) {
					//Se elimina el contacto anterior y se crea el nuevo
					//con los campos que no vienne en el formulario
					$contacto->tercero_id = $obj->id;
					$contacto->tipo_contacto = $contactoAnterior->tipo_contacto;
					$contacto->estrato = $contactoAnterior->estrato;
					$contacto->tipo_vivienda_id = $contactoAnterior->tipo_vivienda_id;
					$contacto->es_preferido = $contactoAnterior->es_preferido;
					$contactoAnterior->delete();
				}
				else {
					$contacto->tercero_id = $obj->id;
					$contacto->tipo_contacto = 'LABORAL';
					$contacto->es_preferido = true;
				}
				$contacto->ciudad_id = $request->ciudad_id;
				$contacto->direccion = $request->direccion;
				$contacto->telefono = $request->telefono;
				$contacto->extension = $request->extension;
				$contacto->movil = $request->movil;
				$contacto->email = $request->email;
				$contacto->save();
			}
			DB::commit();
		}
		catch(Exception $e) {
			DB::rollBack();
			Log::error('Error actualizando información del tercero: ' . $obj->id  . ' - ' . $e->getMessage());
			abort(500, 'Error actualizando información del tercero');
		}
		Session::flash('message', 'Se ha actualizado el tercero \'' . $obj->nombre_corto . '\'');
		return redirect('tercero');
	}

	/**
	 * Obtiene terceros con parámetros
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getTerceroConParametros(Request $request) {
		$validator = Validator::make($request->all(),
			[
				'q'						=> 'bail|string|min:2|max:100',
				'id'					=> 'bail|integer|min:1',
				'tipo'					=> 'bail|string|in:NATURAL,JURÍDICA',
				'tipoIdentificacion'	=> 'bail|string|min:1|max:4',
				'estado'				=> 'bail|string|in:ACTIVO,INACTIVO',
				'tipoCoincidencia'		=> 'bail|string|in:COMPLETA,INCOMPLETA'
			],
			[
				'q.min' => 'La :attribute debe tener mínimo :min caracteres',
				'q.max' => 'La :attribute debe tener máximo :max caracteres',
			],
			[
				'q' => 'consulta'
			]
		);

		if($validator->fails()) {
			return response()->json($validator->errors(), 422, [], JSON_UNESCAPED_UNICODE);
		}

		$coincidenciaCompleta = false;
		if(!empty($request->tipoCoincidencia)) {
			$coincidenciaCompleta = $request->tipoCoincidencia == 'COMPLETA' ? true : false;
		}
		else {
			$coincidenciaCompleta = false;
		}

		$terceros = Tercero::entidadTercero();

		//Con id
		if(!empty($request->id))$terceros->whereId($request->id);

		//Con tipo de tercero
		if(!empty($request->tipo))$terceros->whereTipoTercero($request->tipo);

		//Con tipo de identificación
		if(!empty($request->tipoIdentificacion)) {
			$terceros->whereHas('tipoIdentificacion', function($query) use($request){
				$query->whereCodigo($request->tipoIdentificacion);
			});
		}

		//Con estado
		if(!empty($request->estado))$terceros->activo($request->estado == 'ACTIVO' ? true : false);

		if($coincidenciaCompleta) {
			if(!empty($request->q)) {
				$terceros->whereNumeroIdentificacion($request->q);
			}
		}
		else {
			if(!empty($request->q)) {
				$terceros->search($request->q);
			}
		}

		$terceros = $terceros->take(20)->get();
		$resultado = array('total_count' => $terceros->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($terceros as $tercero) {
			$item = array(
							'id' => $tercero->id,
							'numeroIdentificacion' => $tercero->numero_identificacion,
							'text' => $tercero->nombre_completo,
							'nombre' => $tercero->nombre,
							'digitoVerificacion' => $tercero->digito_verificacion
			);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public function dv(Request $request) {
		$validator = Validator::make($request->all(),
			['numeroIdentificacion' => 'required|digits_between:1,9',],
			[],
			['numeroIdentificacion' => 'número de identificación']
		);

		if($validator->fails()) {
			return response()->json($validator->errors(), 422, [], JSON_UNESCAPED_UNICODE);
		}

		$dv['digitoVerificacion'] = Tercero::digitoVerificacion($request->numeroIdentificacion);
		return response()->json($dv);
	}

	public static function routes() {
		Route::get('tercero', 'General\TerceroController@index');
		Route::get('tercero/create', 'General\TerceroController@create');
		Route::post('tercero', 'General\TerceroController@store');
		Route::get('tercero/{obj}/edit', 'General\TerceroController@edit')->name('terceroEdit');
		Route::put('tercero/{obj}', 'General\TerceroController@update');

		Route::get('tercero/getTerceroConParametros', 'General\TerceroController@getTerceroConParametros');
		Route::get('tercero/dv', 'General\TerceroController@dv');
	}
}
