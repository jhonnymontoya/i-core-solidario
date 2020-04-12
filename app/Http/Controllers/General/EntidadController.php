<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Entidad\CreateComiteCarteraRequest;
use App\Http\Requests\General\Entidad\CreateComiteRiesgoLiquidezRequest;
use App\Http\Requests\General\Entidad\CreateControlSocialRequest;
use App\Http\Requests\General\Entidad\CreateDirectivoRequest;
use App\Http\Requests\General\Entidad\CreateEntidadRequest;
use App\Http\Requests\General\Entidad\CreateRepresentanteLegalRequest;
use App\Http\Requests\General\Entidad\EditEntidadRequest;
use App\Models\General\CategoriaImagen;
use App\Models\General\Ciiu;
use App\Models\General\Ciudad;
use App\Models\General\Contacto;
use App\Models\General\Entidad;
use App\Models\General\Organismo;
use App\Models\General\Tercero;
use App\Models\General\TipoIdentificacion;
use App\Models\Sistema\Usuario;
use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Image;
use Log;
use Route;
use Storage;
use Validator;
use Illuminate\Support\Str;

class EntidadController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin')->except([
		                            	'agregarDirectivo',
		                            	'eliminarOrganismo',
		                            	'agregarRepresentanteLegal',
		                            	'agregarControlSocial',
		                            	'agregarComiteCartera',
		                            	'agregarComiteRiesgoLiquidez'
		]);
		$this->middleware('verEnt')->except([
		                                'dv',
		                                'seleccion',
		                                'seleccionar',
		                                'agregarDirectivo',
		                                'eliminarOrganismo',
		                                'agregarRepresentanteLegal',
		                                'agregarControlSocial',
		                                'agregarComiteCartera',
		                                'agregarComiteRiesgoLiquidez'
		]);
		$this->middleware('verMenu')->except([
		                                'seleccion',
		                                'seleccionar',
		                                'agregarDirectivo',
		                                'eliminarOrganismo',
		                                'agregarRepresentanteLegal',
		                                'agregarControlSocial',
		                                'agregarComiteCartera',
		                                'agregarComiteRiesgoLiquidez'
		]);
	}

	public function index(Request $request) {
		$this->log('Ingresó a consultas de entidades', 'CONSULTAR');
		$entidades = Entidad::search($request->get('name'))->paginate();
		return view('general.entidad.index')->withEntidades($entidades);
	}

	public function create() {
		$this->log('Ingresó a crear nueva entidad', 'INGRESAR');
		return view('general.entidad.create');
	}

	public function store(CreateEntidadRequest $request) {
		$this->log('Ingresó a crear nueva entidad', 'INGRESAR');
		$entidades = Entidad::with('terceroEntidad')->get();
		if($entidades->count() > 0) {
			$validador = Validator::make([], []);
			foreach($entidades as $entidad) {
				if($request->nit == $entidad->terceroEntidad->numero_identificacion) {
					$validador->errors()->add('nit', 'Entidad ya existe');
					return redirect('entidad/create')->withErrors($validador)->withInput();
				}
			}
		}

		$tercero = new Tercero;
		$entidad = new Entidad;

		$nit = TipoIdentificacion::activo(true)->where('aplicacion', 'JURÍDICA')->where('codigo', 'NIT')->first();

		$actividadEconomica = Ciiu::find($request->actividad_economica);
		$tercero->tipo_tercero							= 'JURÍDICA';
		$tercero->tipoIdentificacion()->associate($nit);
		$tercero->numero_identificacion					= $request->nit;
		$tercero->razon_social							= $request->razon;
		$tercero->sigla									= $request->sigla;
		$tercero->actividadEconomica()->associate($actividadEconomica);
		$entidad->fecha_inicio_contabilidad				= $request->fecha_inicio_contabilidad;
		$entidad->usa_dependencias						= $request->usa_dependencia;
		$entidad->usa_centro_costos						= $request->usa_centro_costos;
		$entidad->pagina_web							= $request->pagina_web;

		try{
			DB::transaction(function() use($tercero, $entidad){
				$tercero->save();
				$entidad->tercero_id = $tercero->id;
				$entidad->save();

				$tercero->entidad_id = $entidad->id;
				$tercero->save();
			});
		}
		catch(Exception $e) {
			Log::error('Error creando la entidad: ' . $e->getMessage());
			abort(500, 'Error al crear la entidad');
		}
		Session::flash('message', 'Se ha creado la entidad \''. $entidad->terceroEntidad->razon_social . '\'');
		return redirect()->route('entidadEdit', $entidad->id);
	}

	public function edit(Entidad $obj) {
		$this->log('Ingresó a editar la entidad: ' . $obj->id, 'INGRESAR');
		$organismos = Organismo::entidad($obj->id)->get();
		return view('general.entidad.edit')->withEntidad($obj)->withOrganismos($organismos);
	}

	public function update(EditEntidadRequest $request, Entidad $obj) {
		$this->log('Ingresó a editar la entidad: ' . $obj->id, 'INGRESAR');
		$obj->terceroEntidad->razon_social						= $request->razon;
		$obj->terceroEntidad->sigla								= $request->sigla;
		$obj->terceroEntidad->esta_activo						= $request->esta_activo;
		$actividadEconomica = Ciiu::find($request->actividad_economica);
		$obj->terceroEntidad->actividadEconomica()->dissociate();
		$obj->terceroEntidad->actividadEconomica()->associate($actividadEconomica);
		$obj->fecha_inicio_contabilidad				= $request->fecha_inicio_contabilidad;
		$obj->usa_dependencias						= $request->usa_dependencia;
		$obj->usa_centro_costos						= $request->usa_centro_costos;
		$obj->pagina_web							= $request->pagina_web;
		$obj->terceroEntidad->fecha_constitucion	= strlen($request->fecha_constitucion) > 0?$request->fecha_constitucion:null;
		$obj->terceroEntidad->numero_matricula		= strlen($request->numero_matricula) > 0?$request->numero_matricula:null;

		$contacto = new Contacto;
		$contacto->direccion = strlen($request->direccion_notificacion) > 0?$request->direccion_notificacion:null;
		$ciudad = Ciudad::find($request->ciudad_direccion_notificacion);
		if($ciudad != null) {
			$contacto->ciudad()->dissociate();
			$contacto->ciudad()->associate($ciudad);
		}
		
		if($contacto->ciudad != null || strlen($contacto->direccion) > 0) {
			$contacto->tipo_contacto = 'LABORAL';
			$obj->terceroEntidad->contactos()->delete();
			$obj->terceroEntidad->contactos()->save($contacto);
		}

		$obj->terceroEntidad->save();
		$obj->save();
		Session::flash('message', 'Se ha actualizado la entidad');
		return redirect()->route('entidadEditImagenes', $obj->id);
	}

	/**
	 * Devuelve el formulario para la edición de imagenes
	 * @return [type] [description]
	 */
	public function editImagenes(Entidad $obj) {
		$this->log('Ingresó a editar imagenes de la entidad: ' . $obj->id, 'INGRESAR');
		$categorias = CategoriaImagen::orderBy('ancho', 'desc')->orderBy('alto', 'desc')->orderBy('nombre')->get();
		return view('general.entidad.informacionImagenes')->withCategorias($categorias)->withEntidad($obj);
	}

	/**
	 * Guarda o actualiza las imágenes de la entidad
	 * @return [type] [description]
	 */
	public function updateImagenes(Entidad $obj, Request $request) {
		$this->log('Ingresó a editar imagenes de la entidad: ' . $obj->id, 'INGRESAR');
		$estado = false;
		$categorias = CategoriaImagen::all();
		foreach($categorias as $categoria) {
			if(!empty($request['imagen' . $categoria->id])) {
				try {
					$value = $request['imagen' . $categoria->id];
					$fileName = 'entidad' . $obj->id . '_' . Str::random(10) . "_" . time() . "_" . $categoria->ancho . 'x' . $categoria->alto . '.jpg';
					$avatar = Image::make($value);

					$avatar = $avatar->orientate();
					$avatar->encode('jpg');

					$avatar->save(storage_path('app/public/entidad/' . $fileName));

					$obj->categoriaImagenes()->detach($categoria);
					$obj->categoriaImagenes()->attach($categoria, ['nombre' => $fileName]);
					$estado = true;
				}
				catch(Exception $e) {
					Log::error('Error guardando imagen: ' . $e->getMessage());
				}
			}	
		}
		if($estado)Session::flash('message', 'Se han actualizado las imagenes');
		return redirect()->route('entidadEditImagenes', $obj->id);
	}

	public function seleccion() {
		$usuario = Usuario::with('perfiles.entidad')->find(Auth::user()->id);
		if($usuario->perfiles->count() == 1) {
			$entidad = $usuario->perfiles->first()->entidad;
			Session::put('entidad', $entidad);
			$this->log('Seleccionó la entidad: ' . $entidad->terceroEntidad->sigla, 'INGRESAR');
			return redirect('dashboard');
		}
		$this->log('Ingresó a selección de entidad', 'INGRESAR');
		return view('general.entidad.select')->withUsuario($usuario);
	}

	public function seleccionar(Request $request) {
		$entidad = Entidad::find($request->entidad);
		if($entidad == null) {
			abort(404, 'No existe entidad');
		}
		Session::put('entidad', $entidad);
		$this->log('Seleccionó la entidad: ' . $entidad->terceroEntidad->sigla, 'INGRESAR');
		return redirect('dashboard');
	}

	//API

	/**
	 * Agregar directivo
	 * @param  CreateDirectivoRequest $request [description]
	 * @return [type]                          [description]
	 */
	public function agregarDirectivo(CreateDirectivoRequest $request) {
		$tercero = Socio::find($request->directivo_socio)->tercero;
		$existe = (boolean)Organismo::entidad($request->directivo_entidad)
					->where('tercero_id', $tercero->id)
					->where('tipo_organo', 'DIRECTIVO')
					->count();
		if($existe) {
			return response($tercero->nombre_corto . ' ya se encuentra como directivo', 422);
		}
		$directivo = new Organismo;
		$directivo->tipo_organo = 'DIRECTIVO';
		$directivo->calidad = $request->directivo_calidad;
		$directivo->fecha_nombramiento = $request->directivo_fecha_nombramiento;
		$directivo->periodos = $request->directivo_periodo;
		$tercero->organismos()->save($directivo);
		$ret = array(
				'id' => $directivo->id,
				'identificacion' => $tercero->identificacion,
				'nombre' => $tercero->nombre_corto,
				'calidad' => $directivo->calidad,
				'fecha_nombramiento' => ''. $directivo->fecha_nombramiento,
				'periodos' => $directivo->periodos,
				'estado' => $tercero->socio->estado
		);
		return response()->json($ret);
	}

	/**
	 * Agregar representante legal
	 * @param  CreateRepresentanteLegalRequest $request [description]
	 * @return [type]                                   [description]
	 */
	public function agregarRepresentanteLegal(CreateRepresentanteLegalRequest $request) {
		$tercero = Tercero::find($request->legal_tercero);
		$existe = (boolean)Organismo::entidad($request->legal_entidad)
					->where('tercero_id', $tercero->id)
					->where('tipo_organo', 'REPRESENTANTE_LEGAL')
					->count();
		if($existe) {
			return response($tercero->nombre_corto . ' ya se encuentra como representante legal', 422);
		}
		$representanteLegal = new Organismo;
		$representanteLegal->tipo_organo = 'REPRESENTANTE_LEGAL';
		$representanteLegal->calidad = $request->legal_calidad;
		$representanteLegal->fecha_nombramiento = $request->legal_fecha_nombramiento;
		$representanteLegal->periodos = $request->legal_periodo;

		$tercero->organismos()->save($representanteLegal);
		$ret = array(
				'id' => $representanteLegal->id,
				'identificacion' => $tercero->identificacion,
				'nombre' => $tercero->nombre_corto,
				'calidad' => $representanteLegal->calidad,
				'fecha_nombramiento' => '' . $representanteLegal->fecha_nombramiento,
				'periodos' => $representanteLegal->periodos,
				'estado' => $tercero->esta_activo
		);
		return response()->json($ret);
	}

	/**
	 * Agregar Control Social
	 * @param  CreateDirectivoRequest $request [description]
	 * @return [type]                          [description]
	 */
	public function agregarControlSocial(CreateControlSocialRequest $request) {
		$tercero = Socio::find($request->social_socio)->tercero;
		$existe = (boolean)Organismo::entidad($request->social_entidad)
					->where('tercero_id', $tercero->id)
					->where('tipo_organo', 'CONTROL_SOCIAL')
					->count();
		if($existe) {
			return response($tercero->nombre_corto . ' ya se encuentra como control social', 422);
		}

		$controlSocial = new Organismo;
		$controlSocial->tipo_organo = 'CONTROL_SOCIAL';
		$controlSocial->calidad = $request->social_calidad;
		$controlSocial->fecha_nombramiento = $request->social_fecha_nombramiento;
		$controlSocial->periodos = $request->social_periodo;

		$tercero->organismos()->save($controlSocial);
		$ret = array(
				'id' => $controlSocial->id,
				'identificacion' => $tercero->identificacion,
				'nombre' => $tercero->nombre_corto,
				'calidad' => $controlSocial->calidad,
				'fecha_nombramiento' => '' . $controlSocial->fecha_nombramiento,
				'periodos' => $controlSocial->periodos,
				'estado' => $tercero->socio->estado
		);
		return response()->json($ret);
	}

	public function agregarComiteCartera(CreateComiteCarteraRequest $request) {
		$tercero = Socio::find($request->comitecartera_socio)->tercero;
		$existe = (boolean)Organismo::entidad($request->comitecartera_entidad)
					->where('tercero_id', $tercero->id)
					->where('tipo_organo', 'COMITE_CARTERA')
					->count();
		if($existe) {
			return response($tercero->nombre_corto . ' ya se encuentra en comité cartera', 422);
		}
		$comiteCartera = new Organismo;
		$comiteCartera->tipo_organo = 'COMITE_CARTERA';
		$comiteCartera->calidad = $request->comitecartera_calidad;
		$comiteCartera->fecha_nombramiento = $request->comitecartera_fecha_nombramiento;
		$comiteCartera->periodos = $request->comitecartera_periodo;
		$tercero->organismos()->save($comiteCartera);
		$ret = array(
				'id' => $comiteCartera->id,
				'identificacion' => $tercero->identificacion,
				'nombre' => $tercero->nombre_corto,
				'calidad' => $comiteCartera->calidad,
				'fecha_nombramiento' => '' . $comiteCartera->fecha_nombramiento,
				'periodos' => $comiteCartera->periodos,
				'estado' => $tercero->socio->estado
		);
		return response()->json($ret);
	}

	public function agregarComiteRiesgoLiquidez(CreateComiteRiesgoLiquidezRequest $request) {
		$tercero = Socio::find($request->comiteriesgoliquidez_socio)->tercero;
		$existe = (boolean)Organismo::entidad($request->comiteriesgoliquidez_entidad)
					->where('tercero_id', $tercero->id)
					->where('tipo_organo', 'COMITE_RIESGO_LIQUIDEZ')
					->count();
		if($existe) {
			return response($tercero->nombre_corto . ' ya se encuentra en comité cartera', 422);
		}
		$comiteRiesgoLiquidez = new Organismo;
		$comiteRiesgoLiquidez->tipo_organo = 'COMITE_RIESGO_LIQUIDEZ';
		$comiteRiesgoLiquidez->calidad = $request->comiteriesgoliquidez_calidad;
		$comiteRiesgoLiquidez->fecha_nombramiento = $request->comiteriesgoliquidez_fecha_nombramiento;
		$comiteRiesgoLiquidez->periodos = $request->comiteriesgoliquidez_periodo;

		$tercero->organismos()->save($comiteRiesgoLiquidez);
		$ret = array(
				'id' => $comiteRiesgoLiquidez->id,
				'identificacion' => $tercero->identificacion,
				'nombre' => $tercero->nombre_corto,
				'calidad' => $comiteRiesgoLiquidez->calidad,
				'fecha_nombramiento' => '' . $comiteRiesgoLiquidez->fecha_nombramiento,
				'periodos' => $comiteRiesgoLiquidez->periodos,
				'estado' => $tercero->socio->estado
		);
		return response()->json($ret);
	}

	/**
	 * eliminar organismo
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function eliminarOrganismo(Request $request) {
		$directivo = Organismo::find($request->id);
		if($directivo != null) {
			$directivo->delete();
		}
		return response()->json(array('estado' => '1'));
	}

	public static function routes() {
		Route::get('entidad', 'General\EntidadController@index');
		Route::get('entidad/create', 'General\EntidadController@create');
		Route::post('entidad', 'General\EntidadController@store');
		Route::get('entidad/{obj}/edit', 'General\EntidadController@edit')->name('entidadEdit');
		Route::put('entidad/{obj}', 'General\EntidadController@update');

		Route::get('entidad/{obj}/imagenes', 'General\EntidadController@editImagenes')->name('entidadEditImagenes');
		Route::put('entidad/{obj}/imagenes', 'General\EntidadController@updateImagenes');

		Route::get('entidad/seleccion', 'General\EntidadController@seleccion');
		Route::post('entidad/seleccion', 'General\EntidadController@seleccionar');
	}
}