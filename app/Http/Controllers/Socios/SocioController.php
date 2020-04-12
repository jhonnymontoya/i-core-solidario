<?php

namespace App\Http\Controllers\Socios;

use App\Certificados\CertificadoTributario;
use App\Events\Socios\SocioAfiliado;
use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Socio\Socio\CreateSocioRequest;
use App\Http\Requests\Socio\Socio\EditAfiliacionRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionBeneficiarioRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionContactoRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionFinancieraRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionLaboralRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionObligacionFinancieraRequest;
use App\Http\Requests\Socio\Socio\EditSocioInformacionTarjetaCreditoRequest;
use App\Http\Requests\Socio\Socio\EditSocioRequest;
use App\Http\Requests\Socio\Socio\SelectSocioConParametrosRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Contacto;
use App\Models\General\InformacionFinanciera;
use App\Models\General\ParametroInstitucional;
use App\Models\General\Sexo;
use App\Models\General\Tercero;
use App\Models\General\TipoIdentificacion;
use App\Models\Recaudos\ControlProceso;
use App\Models\Recaudos\Pagaduria;
use App\Models\Recaudos\RecaudoNomina;
use App\Models\Sistema\UsuarioWeb;
use App\Models\Socios\Beneficiario;
use App\Models\Socios\EstadoCivil;
use App\Models\Socios\ObligacionFinanciera;
use App\Models\Socios\Parentesco;
use App\Models\Socios\Socio;
use App\Models\Socios\TarjetaCredito;
use App\Models\Socios\TipoVivienda;
use App\Models\Tesoreria\Banco;
use App\Models\Tesoreria\Franquicia;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Route;
use Session;
use Validator;
use Illuminate\Support\Str;

class SocioController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin')->except(['socio']);
		$this->middleware('verEnt')->except(['socio']);
		$this->middleware('verMenu')->except(['socio']);
	}
	
	public function index(Request $request) {
		$request->validate([
			'name'	=> 'bail|nullable|string|max:300',
			'pagaduria' => 'bail|nullable|integer|min:1',
			'estados' => 'bail|nullable|string|in:ACTIVO,NOVEDAD,RETIRO,LIQUIDADO,PROCESO',

		]);
		$this->logActividad("Ingresó a socios", $request);
		$terceros = Tercero::entidadTercero()->with('socio')
						->whereHas('socio', function($q) use($request){
							$q->pagaduria($request->pagaduria);
							$q->estado($request->estado);
						})
						->search($request->name)->socioTercero()->activo()
						->join('socios.socios', 'socios.tercero_id', '=', 'terceros.id')
						->orderBy('socios.estado', 'asc')->select('terceros.*')->paginate();
		$porcentajeMaximoEndeudamientoPermitido = ParametroInstitucional::entidadId()->codigo('CR003')->first();
		$porcentajeMaximoEndeudamientoPermitido = empty($porcentajeMaximoEndeudamientoPermitido) ? 100 : $porcentajeMaximoEndeudamientoPermitido->valor;
		$pagadurias = Pagaduria::entidadId()->orderBy('nombre')->pluck('nombre', 'id');
		return view('socios.socio.index')->withTerceros($terceros)->withPorcentajeMaximoEndeudamientoPermitido($porcentajeMaximoEndeudamientoPermitido)->withPagadurias($pagadurias);
	}

	public function create() {
		$this->log("Ingresó a crear socio");
		$tiposIdentificacion = TipoIdentificacion::aplicacion('NATURAL')->activo(true)->pluck('nombre', 'id');
		$sexos = Sexo::pluck('nombre', 'id');
		$estadosCiviles = EstadoCivil::entidadId()->pluck('nombre', 'id');

		return view('socios.socio.create')
				->withTiposIdentificacion($tiposIdentificacion)
				->withSexos($sexos)
				->withEstadosCiviles($estadosCiviles);
	}

	public function store(CreateSocioRequest $request) {
		$tercero = Tercero::entidadTercero()
			->whereTipoTercero('NATURAL')
			->whereTipoIdentificacionId($request->tipo_identificacion)
			->whereNumeroIdentificacion($request->identificacion)
			->first();

		// Si no existe el tercero, se crea la instancia del mismo
		if($tercero == null) {
			$tercero = new Tercero;
			$tercero->entidad_id				= $this->getEntidad()->id;
			$tercero->tipo_tercero				= 'NATURAL';
			$tercero->tipo_identificacion_id	= $request->tipo_identificacion;
			$tercero->numero_identificacion		= $request->identificacion;
		}

		$tercero->primer_nombre							= $request->primer_nombre;
		$tercero->segundo_nombre						= empty($request->segundo_nombre) ? null : $request->segundo_nombre;
		$tercero->primer_apellido						= $request->primer_apellido;
		$tercero->segundo_apellido						= empty($request->segundo_apellido) ? null : $request->segundo_apellido;
		$tercero->fecha_nacimiento						= empty($request->fecha_nacimiento) ? null : $request->fecha_nacimiento;
		$tercero->ciudad_nacimiento_id					= empty($request->ciudad_nacimiento) ? null : $request->ciudad_nacimiento;
		$tercero->fecha_expedicion_documento_identidad	= empty($request->fecha_exp_doc_id) ? null : $request->fecha_exp_doc_id;
		$tercero->ciudad_expedicion_documento_id		= empty($request->ciudad_exp_doc_id) ? null : $request->ciudad_exp_doc_id;
		$tercero->sexo_id								= empty($request->sexo) ? null : $request->sexo;
		$tercero->es_asociado							= true;

		$socio = new Socio;		
		$socio->estado								= 'PROCESO';
		$socio->estado_civil_id						= empty($request->estado_civil) ? null : $request->estado_civil;
		$socio->es_mujer_cabeza_familia				= $request->mujer_cabeza_familia;

		$banco = Banco::entidadBanco()->activo()->whereId($request->transferencia_banco_id)->first();
		
		try {
			DB::transaction(function() use($tercero, $socio, $banco, $request) {
				//Se guarda el tercero
				$tercero->save();

				//Guardamos el socio y se asocia al tercero
				$tercero->socio()->save($socio);

				//Se guarda el banco
				if($banco != null && !empty($request->transferencia_numero_cuenta)) {
					$tercero->banco()->detach();
					$tercero->banco()->attach($banco, ['tipo_cuenta' => $request->transferencia_tipo_cuenta, 'numero' => $request->transferencia_numero_cuenta]);
				}
			});
		}
		catch(Exception $e) {
			Log::error('Error creando el socio: ' . $e->getMessage());
			abort(500, 'Error al crear el socio');
		}

		Session::flash('message', 'Se ha guardado el socio \'' . $tercero->nombre_corto . '\', a partir de ahora puede complementar la información faltante y formalizar la afiliación');
		return redirect()->route('socioEditLaboral', [$tercero->socio->id]);
	}

	public function edit(Socio $obj) {
		$this->log("Ingresó a editar el socio '" . $obj->tercero->nombre . "'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$sexos = Sexo::pluck('nombre', 'id');
		$estadosCiviles = EstadoCivil::entidadId()->pluck('nombre', 'id');
		return view('socios.socio.edit')
				->withSexos($sexos)
				->withEstadosCiviles($estadosCiviles)
				->withSocio($obj);
	}

	/**
	 * Modifica la información básica del socio
	 * @param  Socio   $obj [description]
	 * @param  Request $r   [description]
	 * @return [type]       [description]
	 */
	public function update(Socio $obj, EditSocioRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$obj->tercero->primer_nombre						= $request->primer_nombre;
		$obj->tercero->segundo_nombre						= empty($request->segundo_nombre) ? null : $request->segundo_nombre;
		$obj->tercero->primer_apellido						= $request->primer_apellido;
		$obj->tercero->segundo_apellido						= empty($request->segundo_apellido) ? null : $request->segundo_apellido;
		$obj->tercero->fecha_nacimiento						= empty($request->fecha_nacimiento) ? null : $request->fecha_nacimiento;
		$obj->tercero->ciudad_nacimiento_id					= empty($request->ciudad_nacimiento) ? null : $request->ciudad_nacimiento;
		$obj->tercero->fecha_expedicion_documento_identidad	= empty($request->fecha_exp_doc_id) ? null : $request->fecha_exp_doc_id;
		$obj->tercero->ciudad_expedicion_documento_id		= empty($request->ciudad_exp_doc_id) ? null : $request->ciudad_exp_doc_id;
		$obj->tercero->sexo_id								= empty($request->sexo) ? null : $request->sexo;
		$obj->tercero->es_asociado							= true;
		
		$obj->estado_civil_id						= empty($request->estado_civil) ? null : $request->estado_civil;
		$obj->es_mujer_cabeza_familia				= $request->mujer_cabeza_familia;

		$banco = Banco::entidadBanco()->activo()->whereId($request->transferencia_banco_id)->first();
		
		try {
			DB::transaction(function() use($obj, $banco, $request) {
				//Se guarda el tercero
				$obj->tercero->save();

				//Guardamos el socio
				$obj->save();

				//Se guarda el banco
				if($banco != null && !empty($request->transferencia_numero_cuenta))
				{
					$obj->tercero->banco()->detach();
					$obj->tercero->banco()->attach($banco, ['tipo_cuenta' => $request->transferencia_tipo_cuenta, 'numero' => $request->transferencia_numero_cuenta]);
				}
			});
		}
		catch(Exception $e) {
			Log::error('Error actualizando información básica del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información básica del socio');
		}
		Session::flash('message', 'Se ha actualizado el socio');
		return redirect()->route('socioEditLaboral', $obj->id);
	}

	/**
	 * Devuelve el formulario de información laboral para el socio
	 * @param  Request $r [description]
	 * @return [type]     [description]
	 */
	public function editLaboral(Socio $obj) {
		$this->log("Ingresó a editar información laboral del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$pagadurias = Pagaduria::entidadId()->pluck('nombre', 'id');
		$tiposContratos = array(
				'INDEFINIDO' => 'indefinido',
				'FIJO' => 'Fijo',
				'SERVICIOS' => 'Servicios',
				'OBRALABOR' => 'Obra labor',
				'APRENDIZ' => 'Aprendiz',
			);
		$jornadasLaborales = array(
				'TIEMPOCOMPLETO' => 'Tiempo completo',
				'TIEMPOPARCIAL' => 'Tiempo parcial',
				'TELETRABAJO' => 'Teletrabajo'
			);
		$periodicidades = array(
				'DIARIO' => 'Diario',
				'SEMANAL' => 'Semanal',
				'DECADAL' => 'Decadal',
				'CATORCENAL' => 'Catorcenal',
				'QUINCENAL' => 'Quincenal',
				'MENSUAL' => 'Mensual',
				'BIMESTRAL' => 'Bimestral',
				'TRIMESTRAL' => 'Trimestral',
				'CUATRIMESTRAL' => 'Cuatrimestral',
				'SEMESTRAL' => 'Semestral',
				'ANUAL' => 'Anual'
			);
		return view('socios.socio.informacionLaboral')
				->withTiposContratos($tiposContratos)
				->withJornadasLaborales($jornadasLaborales)
				->withPeriodicidades($periodicidades)
				->withSocio($obj)
				->withPagadurias($pagadurias);
	}

	/**
	 * Modifica la información de información laboral del socio
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateLaboral(Socio $obj, EditSocioInformacionLaboralRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$obj->pagaduria_id						= $request->pagaduria_id;
		$obj->cargo								= $request->cargo;
		$obj->profesion_id						= empty($request->profesion) ? null : $request->profesion;
		$obj->fecha_ingreso						= $request->fecha_ingreso_empresa;
		$obj->tipo_contrato						= $request->tipo_contrato;
		if($obj->tipo_contrato == 'INDEFINIDO' || $obj->tipo_contrato == 'OBRALABOR') {
			$obj->fecha_fin_contrato				= null;
		}
		else {
			$obj->fecha_fin_contrato				= empty($request->fecha_fin_contrato) ? null : $request->fecha_fin_contrato;
		}
		$obj->jornada_laboral						= $request->jornada_laboral;
		$obj->codigo_nomina							= $request->codigo_nomina;
		$obj->sueldo_mes							= $request->sueldo_mensual;
		$obj->valor_comision						= empty($request->valor_comision) ? null : $request->valor_comision;
		$obj->periodicidad_comision					= empty($request->periodicidad_comision) ? null : $request->periodicidad_comision;
		$obj->valor_prima							= empty($request->valor_prima) ? null : $request->valor_prima;
		$obj->periodicidad_prima					= empty($request->periodicidad_prima) ? null : $request->periodicidad_prima;
		$obj->valor_extra_prima						= empty($request->valor_extra_prima) ? null : $request->valor_extra_prima;
		$obj->periodicidad_extra_prima				= empty($request->periodicidad_extra_prima) ? null : $request->periodicidad_extra_prima;
		$obj->descuentos_nomina						= empty($request->valor_descuento_nomina) ? null : $request->valor_descuento_nomina;
		$obj->periodicidad_descuentos_nomina		= empty($request->periodicidad_descuento_nomina) ? null : $request->periodicidad_descuento_nomina;
		$obj->descuento_prima						= empty($request->valor_descuento_prima) ? null : $request->valor_descuento_prima;
		$obj->periodicidad_descuento_prima			= empty($request->periodicidad_descuento_prima) ? null : $request->periodicidad_descuento_prima;
		$obj->descuento_extra_prima					= empty($request->valor_descuento_extra_prima) ? null : $request->valor_descuento_extra_prima;
		$obj->periodicidad_descuento_extra_prima	= empty($request->periodicidad_descuento_extra_prima) ? null : $request->periodicidad_descuento_extra_prima;

		$obj->tercero->actividad_economica_id		= empty($request->actividad_economica) ? null : $request->actividad_economica;

		try {
			DB::transaction(function() use($obj) {
				//Se guarda el tercero
				$obj->tercero->save();

				//Guardamos el socio
				$obj->save();
			});
		}
		catch(Exception $e) {
			Log::error('Error actualizando información laboral del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información laboral del socio');
		}

		Session::flash('message', 'Se ha actualizado el socio');
		return redirect()->route('socioEditContacto', $obj->id);
	}

	/**
	 * Devuelve el formulario de edición de contactos para socio
	 * @return [type] [description]
	 */
	public function editContacto(Socio $obj) {
		$this->log("Ingresó a editar la información de contacto del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$tiposViviendas = TipoVivienda::entidadTipoVivienda()->orderBy('nombre')->pluck('nombre', 'id');
		return view('socios.socio.informacionContacto')
				->withTiposViviendas($tiposViviendas)
				->withSocio($obj);
	}

	/**
	 * Modifica la información de contacto para el socio
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateContacto(Socio $obj, EditSocioInformacionContactoRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$contactoResidencial = new Contacto;

		$contactoResidencial->tipo_contacto		= 'RESIDENCIAL';
		$contactoResidencial->ciudad_id			= empty($request->ciudad_residencial) ? null : $request->ciudad_residencial;
		$contactoResidencial->direccion			= $request->direccion_residencial;
		$contactoResidencial->estrato			= empty($request->estrato_vivienda) ? null : $request->estrato_vivienda;
		$contactoResidencial->tipo_vivienda_id	= empty($request->tipo_vivienda) ? null : $request->tipo_vivienda;
		$contactoResidencial->email				= $request->email_residencia;
		$contactoResidencial->telefono			= $request->telefono_residencia;
		$contactoResidencial->movil				= $request->celular_residencia;
		$contactoResidencial->es_preferido		= $request->preferencia_envio_residencia;

		$contactoLaboral = new Contacto;

		$contactoLaboral->tipo_contacto			= 'LABORAL';
		$contactoLaboral->ciudad_id				= empty($request->ciudad_laboral) ? null : $request->ciudad_laboral;
		$contactoLaboral->direccion				= $request->direccion_laboral;
		$contactoLaboral->email					= $request->email_laboral;
		$contactoLaboral->telefono				= $request->telefono_laboral;
		$contactoLaboral->extension				= $request->extension_laboral;
		$contactoLaboral->movil					= $request->celular_laboral;
		$contactoLaboral->es_preferido			= $request->preferencia_envio_laboral;

		try {
			DB::transaction(function() use($obj, $contactoResidencial, $contactoLaboral) {
				
				foreach($obj->tercero->contactos as $contacto) {
					if($contacto->tipo_contacto == 'RESIDENCIAL' && $contactoResidencial->hayCampos()) {
						$contacto->delete();
					}
					if($contacto->tipo_contacto == 'LABORAL' && $contactoLaboral->hayCampos()) {
						$contacto->delete();
					}
				}

				//Se guardan los contactos
				if($contactoResidencial->hayCampos())$obj->tercero->contactos()->save($contactoResidencial);
				if($contactoLaboral->hayCampos())$obj->tercero->contactos()->save($contactoLaboral);
			});
		}
		catch(Exception $e) {
			Log::error('Error actualizando información de contacto del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información de contacto del socio.');
		}

		Session::flash('message', 'Se ha actualizado el socio');
		return redirect()->route('socioEditBeneficiarios', $obj->id);
	}
	
	/**
	 * Devuelve el formulario para edición de información financiera de socios
	 * @param  Socio  $obj [description]
	 * @return [type]      [description]
	 */
	public function editFinanciera(Socio $obj) {
		$this->log("Ingresó a editar la información financiera del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		return view('socios.socio.informacionFinanciera')->withSocio($obj);
	}
	
	/**
	 * Modifica la información financiera de socios
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateFinanciera(Socio $obj, EditSocioInformacionFinancieraRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$informacionFinanciera = new InformacionFinanciera;

		$informacionFinanciera->activos				= $request->activos;
		$informacionFinanciera->pasivos				= $request->pasivos;
		$informacionFinanciera->ingreso_mensual		= $request->otros_ingresos;
		$informacionFinanciera->gasto_mensual		= $request->egresos_mensuales;
		$informacionFinanciera->fecha_corte			= $request->fecha_corte;

		try {
			DB::transaction(function() use($obj, $informacionFinanciera) {
				if($informacionFinanciera->hayCampos())
				{
					$obj->tercero->informacionesFinancieras()->delete();
					$obj->tercero->informacionesFinancieras()->save($informacionFinanciera);
				}
			});
		}
		catch(Exception $e) {
			Log::error('Error actualizando información financiera del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información financiera del socio');
		}
		Session::flash('message', 'Se ha actualizado el socio');
		return redirect()->route('socioEditTarjetasCredito', $obj->id);
	}
	
	/**
	 * Devuelve el formulario para edición de beneficiarios de socios
	 * @param  Socio  $obj [description]
	 * @return [type]      [description]
	 */
	public function editBeneficiarios(Socio $obj) {
		$this->log("Ingresó a editar los beneficiarios del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$beneficiarios = Beneficiario::socioid($obj->id)->paginate();
		$tiposIdentificacion = TipoIdentificacion::aplicacion('NATURAL')->activo(true)->pluck('nombre', 'id');
		$parentescos = Parentesco::orderBy('nombre')->pluck('nombre', 'id');
		$total = 0;
		foreach($obj->beneficiarios as $beneficiario)$total += $beneficiario->porcentaje_beneficio;
		return view('socios.socio.informacionBeneficiarios')
				->withTiposIdentificacion($tiposIdentificacion)
				->withParentescos($parentescos)
				->withBeneficiarios($beneficiarios)
				->withTotal($total)
				->withSocio($obj);
	}

	/**
	 * Modifica la información de beneficiarios de socios
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateBeneficiarios(Socio $obj, EditSocioInformacionBeneficiarioRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$tercero = Tercero::entidadTercero($obj->tercero->entidad->id)
			->whereTipoTercero('NATURAL')
			->whereTipoIdentificacionId($request->tipo_identificacion)
			->whereNumeroIdentificacion($request->identificacion)
			->first();

		if($tercero == null) {
			$tercero = new Tercero;

			$tercero->entidad_id				= $obj->tercero->entidad->id;
			$tercero->tipo_tercero				= 'NATURAL';
			$tercero->tipo_identificacion_id	= $request->tipo_identificacion;
			$tercero->numero_identificacion		= $request->identificacion;

			$nombres = explode(' ', $request->nombres);
			$apellidos = explode(' ', $request->apellidos);

			$tercero->primer_nombre = array_shift($nombres);
			$tercero->segundo_nombre = count($nombres) > 0 ? implode(' ', $nombres) : null;
			$tercero->primer_apellido = array_shift($apellidos);
			$tercero->segundo_apellido = count($apellidos) > 0 ? implode(' ', $apellidos) : null;
		}

		$total = 0;
		foreach($obj->beneficiarios as $beneficiario)$total += $beneficiario->porcentaje_beneficio;

		if($total + $request->beneficio > 100) {
			return redirect()
							->back()
							->withErrors(['beneficio' => 'La suma de los beneficios no debe superrar el 100%'])
							->withInput();
		}

		try {
			DB::beginTransaction();
			//Se guarda el tercero
			$tercero->save();

			//Se busca que el beneficiario ya no se encuentre para el socio
			$beneficiario = Beneficiario::whereTerceroId($tercero->id)->whereSocioId($obj->id)->first();

			//si se encuentra el beneficiario para el socio, se devuelve un error
			if($beneficiario != null) {
				DB::rollBack();
				return redirect()
							->back()
							->withErrors(['identificacion' => 'El tercero ya se encuentra registrado como beneficiario para el socio'])
							->withInput();
			}

			$beneficiario = new Beneficiario;

			$beneficiario->socio_id = $obj->id;
			$beneficiario->parentesco_id = $request->parentesco;
			$beneficiario->porcentaje_beneficio = $request->beneficio;
			
			$tercero->beneficiarios()->save($beneficiario);
			DB::commit();
		}
		catch(Exception $e) {
			DB::rollBack();
			Log::error('Error actualizando información beneficiarios del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información beneficiarios del socio');
		}

		Session::flash('message', 'Se ha creado el beneficiario');
		if($total + $request->beneficio == 100) {
			return redirect()->route('socioEditImagenes', $obj->id);
		}
		else {
			return redirect()->route('socioEditBeneficiarios', $obj->id);
		}
	}

	/**
	 * Elimina un beneficiario del socio
	 * @param  Socio   $obj     [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function deleteBeneficiarios(Socio $obj, Request $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		//Verificamos que el id no venga vacio
		if(empty($request->id))return redirect()->back()->withErrors(['Id vacio'])->withInput();

		//Consultamos el beneficiario
		$beneficiario = Beneficiario::find($request->id);

		//Verificamos que el beneficiario no sea nulo
		if($beneficiario == null)return redirect()->back()->withErrors(['Beneficiario no existe'])->withInput();

		//Verificamos que el dueño del beneficiario sea el socio
		if($beneficiario->socio->id != $obj->id)return redirect()->back()->withErrors(['Beneficiario no cioincide con el socio'])->withInput();

		//Se elimina el beneficiario
		$beneficiario->delete();

		Session::flash('message', 'Se ha eliminado el beneficiario');
		return redirect()->route('socioEditBeneficiarios', $obj->id);
	}

	/**
	 * Devuelve el formulario para edición de tarjetas de crédito de socios
	 * @param  Socio  $obj [description]
	 * @return [type]      [description]
	 */
	public function editTarjetasCredito(Socio $obj) {
		$this->log("Ingresó a editar la información de tarjetas de crédito del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$franquicias = Franquicia::entidadId($obj->tercero->entidad->id)->pluck('nombre', 'id');
		$tarjetasDeCredito = TarjetaCredito::socioId($obj->id)->paginate();
		return view('socios.socio.informacionTarjetasCredito')->withFranquicias($franquicias)->withTarjetas($tarjetasDeCredito)->withSocio($obj);
	}

	/**
	 * Modifica la información de tarjetas de crédito para socios
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateTarjetasCredito(Socio $obj, EditSocioInformacionTarjetaCreditoRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$tarjeta = null;
		try {
			if(!empty($request->anio)) {
				$tarjeta = new TarjetaCredito;

				$tarjeta->franquicia_id 	= $request->franquicia;
				$tarjeta->banco_id			= $request->banco;
				$tarjeta->anio_vencimiento	= $request->anio;
				$tarjeta->mes_vencimiento	= $request->mes;
				$tarjeta->cupo				= empty($request->cupo) ? null : $request->cupo;
				$tarjeta->saldo				= empty($request->saldo) ? null : $request->saldo;

				$obj->tarjetasCredito()->save($tarjeta);
			}
		}
		catch(Exception $e) {
			Log::error('Error actualizando información de tarjetas de crédito del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información de tarjetas de crédito del socio');
		}
		if($tarjeta != null)Session::flash('message', 'Se ha guardado la tarjeta de crédito');
		return redirect()->route('socioEditTarjetasCredito', $obj->id);
	}

	/**
	 * Elimina una tarjeta de crédito del socio
	 * @param  Socio   $obj     [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function deleteTarjetasCredito(Socio $obj, TarjetaCredito $tarjetaCredito) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		/*Se valida que la tarjeta de crédito pertenezca al socio*/
		if($tarjetaCredito->socio_id != $obj->id) {
			Session::flash('error', 'No es posible eliminar la tarjeta de crédito ya que no pertence al socio');
			return redirect()->back()->withInput();
		}

		//Se elimina la tarjeta de crédito
		$tarjetaCredito->delete();

		Session::flash('message', 'Se ha eliminado la tarjeta de crédito');
		return redirect()->route('socioEditTarjetasCredito', $obj->id);
	}
	
	/**
	 * Devuelve el formulario para edición de obligaciones financieras de socios
	 * @param  Socio  $obj [description]
	 * @return [type]      [description]
	 */
	public function editObligacionesFinancieras(Socio $obj) {
		$this->log("Ingresó a editar la información financiera del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$obligaciones = ObligacionFinanciera::socioId($obj->id)->paginate();
		return view('socios.socio.informacionObligacionesFinancieras')->withObligaciones($obligaciones)->withSocio($obj);
	}

	/**
	 * Modifica la información de obligaciones financieras para socios
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateObligacionesFinancieras(Socio $obj, EditSocioInformacionObligacionFinancieraRequest $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$obligacion = null;
		try {
			if(!empty($request->monto)) {
				$obligacion = new ObligacionFinanciera;

				$obligacion->banco_id = $request->banco;
				$obligacion->fill($request->all());

				$obj->obligacionesFinancieras()->save($obligacion);
			}
		}
		catch(Exception $e) {
			Log::error('Error actualizando información de obligaciones financieras del socio: ' . $e->getMessage());
			abort(500, 'Error actualizando información de obligaciones financieras del socio');
		}
		if($obligacion != null)Session::flash('message', 'Se ha guardado la obligacion financiera');
		return redirect()->route('socioEditObligacionesFinancieras', $obj->id);
	}

	/**
	 * Elimina una obligación financiera del socio
	 * @param  Socio   $obj     [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function deleteObligacionesFinancieras(Socio $obj, ObligacionFinanciera $obligacionFinanciera) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		/*Se valida que la obligación financiera pertenezca al socio*/
		if($obligacionFinanciera->socio_id != $obj->id) {
			Session::flash('error', 'No es posible eliminar la obligación financiera ya que no pertence al socio');
			return redirect()->back()->withInput();
		}
		//Se elimina la obligación financiera
		$obligacionFinanciera->delete();

		Session::flash('message', 'Se ha eliminado la obligación financiera');
		return redirect()->route('socioEditObligacionesFinancieras', $obj->id);
	}
	
	/**
	 * Devuelve el formulario para edición de imagenes de socios
	 * @param  Socio  $obj [description]
	 * @return [type]      [description]
	 */
	public function editImagenes(Socio $obj) {
		$this->log("Ingresó a editar la imagen del socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		return view('socios.socio.informacionImagenes')->withSocio($obj);
	}

	/**
	 * Modifica la información de imágenes de socios
	 * @param  Socio   $obj Socio al que se le va a modificar la información
	 * @param  Request $r   Datos a modificar
	 * @return [type]       [description]
	 */
	public function updateImagenes(Socio $obj, Request $request) {
		$this->log("Actualizó la imagen del socio '$obj->tercero->nombre'", "ACTUALIZAR");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$estado = false;
		if(!empty($request->imagen)) {
			$obj->avatar = $request->imagen;
			$estado = true;
		}

		if(!empty($request->firma)) {
			$estado = true;
			$obj->firma = $request->firma;
		}

		$obj->save();

		if($estado)Session::flash('message', 'Se ha actualizado las imagenes');
		return redirect()->route('socioEditFinanciera', $obj->id);
	}

	public function editAfiliacion(Socio $obj) {
		$this->log("Ingresó a afiliar el socio '$obj->tercero->nombre'");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		if($obj->estado == 'ACTIVO' || $obj->estado == 'NOVEDAD') {
			return redirect('socio');
		}
		return view('socios.socio.afiliar')->withSocio($obj);
	}

	public function updateAfiliacion(Socio $obj, EditAfiliacionRequest $request) {
		$this->log("Porcesó la afiliación del socio '$obj->tercero->nombre'", "ACTUALIZAR");
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		if($this->moduloCerrado(10, $request->fecha_afiliacion)) {
			return redirect()->back()
				->withErrors(['fecha_afiliacion' => 'La fecha de afiliación corresponde a un periodo ya cerrado'])
				->withInput();
		}
		$diasMinimosParaReingreso = ParametroInstitucional::entidadId()->codigo('SO002')->first();
		if(!$diasMinimosParaReingreso) {
			$diasMinimosParaReingreso = new ParametroInstitucional;
			$diasMinimosParaReingreso->valor = 0;
		}
		//SE VALIDA LOS DÍAS MÍNIMOS PARA REINTEGRO CUANDO EL SOCIO SE HA RETIRADO
		if($obj->fecha_retiro) {
			$diasRetiroAFechaAfiliacion = $obj->fecha_retiro->diffInDays(Carbon::createFromFormat('d/m/Y', $request->fecha_afiliacion));
			if($diasRetiroAFechaAfiliacion < $diasMinimosParaReingreso->valor) {
				return redirect()->back()
					->withErrors(['fecha_afiliacion' => 'El socio no cumple con los días mínimos para reintegro'])
					->withInput();
			}
		}
		$obj->fecha_afiliacion 			= $request->fecha_afiliacion;
		$obj->fecha_antiguedad 			= $request->fecha_antiguedad;
		$obj->comentario				= $request->comentario;
		$obj->referido_por_tercero_id 	= $request->referido;
		$obj->estado					= 'ACTIVO';
		$obj->fecha_retiro				= null;

		//Validamos que la fecha de afiliación sea mayor o igual de la fecha de ingreso a la empresa
		if($obj->fecha_afiliacion < $obj->fecha_ingreso)
			return redirect()->back()
				->withErrors(['fecha_afiliacion' => 'La fecha de afiliación no puede ser menor a la fecha de ingreso empresa (' . $obj->fecha_ingreso . ')'])
				->withInput();

		//Validamos que la fecha de afiliación sea menor a la fecha fin de contrato, si esta existe
		if(!empty($obj->fecha_fin_contrato)) {
			if($obj->fecha_afiliacion > $obj->fecha_fin_contrato) {
				return redirect()->back()
					->withErrors(['fecha_afiliacion' => 'La fecha de afiliación no puede ser mayor a la fecha de fin contrato (' . $obj->fecha_fin_contrato . ')'])
					->withInput();
			}
		}

		//Validamos que el referido no sea el mismo socio
		if($obj->referido_por_tercero_id == $obj->tercero->id) {
			return redirect()->back()->withErrors(['referido' => 'El referido no puede ser el mismo socio'])->withInput();
		}

		/*AQUI VALIDACIÓN DE PARAMETROS DE AFILIACIÓN*/
		$camposFaltantes = collect([]);
		if(!$obj->tercero->contactos->count()) {
			$camposFaltantes->put('contacto', '<a href="' . route('socioEditContacto', $obj) . '">Dirección de contacto</a>');
		}
		if(empty($obj->sueldo_mes)) {
			$camposFaltantes->put('sueldo', '<a href="' . route('socioEditLaboral', $obj) . '">Sueldo</a>');
		}
		if(empty($obj->jornada_laboral)) {
			$camposFaltantes->put('jornada', '<a href="' . route('socioEditLaboral', $obj) . '">Jornada laboral</a>');
		}
		if(empty($obj->tipo_contrato)) {
			$camposFaltantes->put('contrato', '<a href="' . route('socioEditLaboral', $obj) . '">Tipo contrato</a>');
		}
		if(empty($obj->fecha_ingreso)) {
			$camposFaltantes->put('fecha_ingreso', '<a href="' . route('socioEditLaboral', $obj) . '">Fecha de ingreso empresa</a>');
		}
		if(empty($obj->pagaduria_id)) {
			$camposFaltantes->put('empresa', '<a href="' . route('socioEditLaboral', $obj) . '">Empresa</a>');
		}

		if($camposFaltantes->count()) {
			return view('socios.socio.afiliar')->withSocio($obj)->withFaltantes($camposFaltantes);
		}
		$mensaje = sprintf('Se ha procesado la afiliación con éxito para el socio %s, ahora debe actualizar las cuotas obligatorias', $obj->tercero->nombre_corto);

		$password = Str::random(8);
		$tercero = $obj->tercero;

		$usuarioWeb = UsuarioWeb::whereUsuario($tercero->numero_identificacion)->first();
		if(empty($usuarioWeb)) {
			$usuarioWeb = new UsuarioWeb;
			$usuarioWeb->usuario = $tercero->numero_identificacion;
		}
		$usuarioWeb->esta_activo = true;
		$usuarioWeb->password = bcrypt($password);
		$usuarioWeb->save();

		$obj->usuario_web_id = $usuarioWeb->id;

		$obj->save();

		event(new SocioAfiliado($obj, $password));

		Session::flash('message', $mensaje);
		return redirect()->route('cuotaObligatoriaCreate', $obj->id);
	}

	public function getSocio(Request $request) {
		if(!empty($request->q)) {
			$socios = Socio::entidad()->estado('ACTIVO')->search($request->q)->limit(20)->get();
		}
		else {
			$socios = Socio::entidad()->estado('ACTIVO')->take(20)->get();
		}

		$resultado = array('total_count' => $socios->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($socios as $socio) {
			$item = array('id' => $socio->id, 'text' => $socio->tercero->nombre_completo);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public function getSocioConParametros(SelectSocioConParametrosRequest $request) {
		$socios = Socio::entidad();

		if(!empty($request->id)) {
			$socios->whereId($request->id);
		}

		if(!empty($request->pagaduria)) {
			$socios->wherePagaduriaId($request->pagaduria);
		}

		if(!empty($request->fechaAntiguedadEmpresaIgualA)) {
			$socios->whereFechaIngreso($request->fechaAntiguedadEmpresaIgualA);
		}

		if(!empty($request->fechaAntiguedadEmpresaMenorA)) {
			$socios->where('fecha_ingreso', '<', $request->fechaAntiguedadEmpresaMenorA);
		}

		if(!empty($request->fechaAntiguedadEmpresaMayorA)) {
			$socios->where('fecha_ingreso', '>', $request->fechaAntiguedadEmpresaMayorA);
		}

		if(!empty($request->fechaAntiguedadFondoIgualA)) {
			$socios->where('fecha_antiguedad', $request->fechaAntiguedadFondoIgualA);
		}

		if(!empty($request->fechaAntiguedadFondoMenorA)) {
			$socios->where('fecha_antiguedad', '<', $request->fechaAntiguedadFondoMenorA);
		}

		if(!empty($request->fechaAntiguedadFondoMayorA)) {
			$socios->where('fecha_antiguedad', '>', $request->fechaAntiguedadFondoMayorA);
		}

		if(!empty($request->tipoContrato)) {
			$socios->where('tipo_contrato', $request->tipoContrato);
		}

		if(!empty($request->tipoContrato)) {
			$socios->where('tipo_contrato', $request->tipoContrato);
		}

		if(!empty($request->sueldoIgualA)) {
			$socios->where('sueldo_mes', $request->sueldoIgualA);
		}

		if(!empty($request->sueldoMenorA)) {
			$socios->where('sueldo_mes', '<', $request->sueldoMenorA);
		}

		if(!empty($request->sueldoMayorA)) {
			$socios->where('sueldo_mes', '>', $request->sueldoMayorA);
		}

		if(!empty($request->estadoIgualA)) {
			$socios->where('estado', $request->estadoIgualA);
		}

		if(!empty($request->estadoDiferenteA)) {
			$socios->where('estado', '<>', $request->estadoDiferenteA);
		}

		if(!empty($request->q)) {
			$socios->search($request->q);
		}

		$socios = $socios->take(20)->get();

		$resultado = array('total_count' => $socios->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($socios as $socio) {
			$item = array('id' => $socio->id, 'text' => $socio->tercero->nombre_completo);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public function consultaSocio(Request $request) {
		$this->logActividad("Ingresó a consulta de socios", $request);
		$entidad = $this->getEntidad();
		$request->validate([
				'fecha'	=> 'bail|nullable|date_format:"d/m/Y"',
				'socio'	=> 'bail|nullable|exists:sqlsrv.socios.socios,id,deleted_at,NULL'
		]);
		$modalidadesCredito = Modalidad::entidadId()->activa()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad)$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;

		$socio = Socio::with('SDATs', 'tercero', 'SDATs.tipoSDAT')
			->entidad()
			->whereId($request->socio)
			->first();

		$fechaConsulta = empty($request->fecha) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $request->fecha)->startOfDay();

		$ahorros = (object)array("saldo" => 0, "variacionAhorro" => 0);
		$creditos = (object)array("saldo" => 0, "porcentajePago" => 0);
		$recaudos = (object)array("aplicado" => 0);
		$porcentajeMaximoEndeudamientoPermitido = 0;
		$recaudoAplicado = null;
		if($socio) {
			$this->objEntidad($socio->tercero);
			//Se obtiene los valores de ahorros del socio
			$sql = "select ahorros.fn_saldo_total_ahorros(?, ?) AS saldo, ahorros.fn_saldo_total_ahorros(?, ?) AS saldo_anterior;";
			$res = DB::select($sql, [$socio->id, $fechaConsulta, $socio->id, $fechaConsulta->copy()->addMonth(-1)]);
			if($res) {
				$saldoAnterior = $res[0]->saldo_anterior;
				$saldo = $res[0]->saldo;
				try{
					$ahorros->variacionAhorro = intval(($saldo * 100) / $saldoAnterior) - 100;
				}
				catch(\ErrorException $e) {
					$ahorros->variacionAhorro = 0;
				}
				$ahorros->saldo = $saldo;
			}
			//Se obtiene los valores de créditos del socio
			$sql = "exec creditos.sp_saldo_total_creditos ?, ?";
			$res = DB::select($sql, [$socio->id, $fechaConsulta]);
			if($res) {
				$creditos->porcentajePago = intval($res[0]->porcentajePago);
				$creditos->saldo = $res[0]->saldo;
			}
			$controlProceso = $socio->pagaduria->controlProceso()->whereEstado('APLICADO')->orderBy('fecha_aplicacion', 'desc')->first();
			$rec = $socio->tercero->recaudosNomina()
				->select(
					DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) + SUM(seguro_aplicado) as total_aplicado'),
				)
				->where('control_proceso_id', $controlProceso->id)
				->get();
			if($rec->count()) {
				$recaudos->aplicado = $rec[0]->total_aplicado;
			}

			$porcentajeMaximoEndeudamientoPermitido = ParametroInstitucional::entidadId($this->getEntidad()->id)->codigo('CR003')->first();
			$porcentajeMaximoEndeudamientoPermitido = empty($porcentajeMaximoEndeudamientoPermitido) ? 100 : $porcentajeMaximoEndeudamientoPermitido->valor;

			//Se consulta el último recaudo aplicado
			$recaudoAplicado = $socio->pagaduria->calendarioRecaudos()
				->whereHas('controlProceso', function($query){
					$query->where('estado', 'APLICADO')
						->orWhere('estado', 'AJUSTADO');
				})
				->where('estado', 'EJECUTADO')
				->orderBy('fecha_recaudo', 'desc')
				->first();
		}
		return view('socios.socio.consulta')
			->withSocio($socio)
			->withAhorros($ahorros)
			->withCreditos($creditos)
			->withRecaudos($recaudos)
			->withRecaudoAplicado($recaudoAplicado)
			->withPorcentajeMaximoEndeudamientoPermitido($porcentajeMaximoEndeudamientoPermitido);
	}

	public function consultaAhorrosLista(Socio $obj, Request $request) {
		$res = $request->validate([
			'fecha' => 'bail|required|date_format:"d/m/Y"'
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $res["fecha"])->startOfDay();
		$this->logActividad("Ingresó a consulta de ahorros lista", $request);
		$this->objEntidad($obj->tercero);

		$ahorros = collect();
		$SDATs = collect();

		//Se obtiene los ahorros del socio
		$res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$obj->id, $fechaConsulta]);
		$ahorros = collect($res);
		$ahorros->transform(function($item, $key) {
			$item->cuotaMes = ConversionHelper::conversionValorPeriodicidad($item->cuota, $item->periodicidad, 'MENSUAL');
			if(!empty($item->vencimiento)) {
				$item->vencimiento = Carbon::createFromFormat('Y/m/d', $item->vencimiento)->startOfDay();
			}
			return $item;
		});

		foreach($obj->SDATs as $sdat) {
			if($sdat->estaActivo() == false) {
				continue;
			}
			$rendimientos = $sdat->rendimientosSdat()->where("fecha_movimiento", '<=', $fechaConsulta)->get();
			$movimientos = $sdat->movimientosSdat()->where("fecha_movimiento", '<=', $fechaConsulta)->get();
			$deposito = (object)[
				"id" => $sdat->id,
				"codigo" => $sdat->tipoSDAT->codigo,
				"valor" => '$' . number_format($sdat->valor),
				"fecha_constitucion" => $sdat->fecha_constitucion,
				"plazo" => $sdat->plazo,
				"fecha_vencimiento" => $sdat->fecha_vencimiento,
				"tasa" => number_format($sdat->tasa, 2) . '%',
				"estado" => $sdat->estado,
				"rendimientos" => '$' . number_format($rendimientos->sum("valor")),
				"saldo" => '$' . number_format($movimientos->sum("valor")),
				"saldo_valor" => $movimientos->sum("valor")
			];
			$SDATs->push($deposito);
		}

		return view('socios.socio.consultaAhorrosLista')
			->withSocio($obj)
			->withAhorros($ahorros)
			->withSdats($SDATs)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaRecaudosLista(Socio $obj, Request $request) {
		$res = $request->validate([
			'fecha' => 'bail|required|date_format:"d/m/Y"'
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $res["fecha"])->startOfDay();
		$this->logActividad("Ingresó a consulta de recaudos lista", $request);
		$this->objEntidad($obj->tercero);

		$recaudos = collect();
		$recaudos = $obj->tercero
			->recaudosNomina()
			->select(
					'control_proceso_id',
					'concepto_recaudo_id',
					DB::raw('SUM(capital_generado) + SUM(intereses_generado) + SUM(seguro_generado) as total_generado'),
					DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) + SUM(seguro_aplicado) as total_aplicado'),
					DB::raw('SUM(capital_ajustado) + SUM(intereses_ajustado) + SUM(seguro_ajustado) as total_ajustado')
			)
			->groupBy('control_proceso_id', 'concepto_recaudo_id')
			->orderBy('control_proceso_id', 'desc')
			->orderBy('concepto_recaudo_id', 'asc')
			->get();

		return view('socios.socio.consultaRecaudosLista')
			->withSocio($obj)
			->withRecaudos($recaudos)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaSocioDocumentacion(Socio $obj, Request $request) {
		$res = $request->validate([
			'fecha' => 'bail|required|date_format:"d/m/Y"'
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $res["fecha"])->startOfDay();
		$this->logActividad("Ingresó a consulta de documentacion", $request);
		$this->objEntidad($obj->tercero);

		return view('socios.socio.consultaDocumentacion')
			->withSocio($obj)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaSocioSimulador(Socio $obj, Request $request) {
		$res = $request->validate([
			'fecha' => 'bail|required|date_format:"d/m/Y"'
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $res["fecha"])->startOfDay();
		$this->logActividad("Ingresó a consulta de simulador", $request);
		$this->objEntidad($obj->tercero);

		$modalidadesCredito = Modalidad::entidadId()->activa()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad)$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;

		return view('socios.socio.consultaSimulador')
			->withSocio($obj)
			->withModalidades($modalidades)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaCreditosLista(Socio $obj, Request $request) {
		$res = $request->validate([
			'fecha' => 'bail|required|date_format:"d/m/Y"'
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $res["fecha"])->startOfDay();
		$this->logActividad("Ingresó a consulta de créditos lista", $request);
		$this->objEntidad($obj->tercero);

		$creditos = collect();
		$codeudas = collect();
		$saldados = collect();		

		$creditos = $obj->tercero
			->solicitudesCreditos()
			->where('fecha_desembolso', '<=', $fechaConsulta)
			->estado('DESEMBOLSADO')
			->get();

		$creditos->transform(function($item, $key) use($fechaConsulta) {
			$item->saldoCapital = $item->saldoObligacion($fechaConsulta);
			$item->saldoIntereses = $item->saldoInteresObligacion($fechaConsulta);
			return $item;
		});

		$cod = $obj->tercero->codeudas()->whereHas('solicitudCredito', function($q){
			return $q->whereEstadoSolicitud('DESEMBOLSADO');
		})->get();

		foreach ($cod as $item) {
			$sc = $item->solicitudCredito;
			$ter = $sc->tercero;
			$nom = "%s %s - %s";
			$nom = sprintf($nom, $ter->tipoIdentificacion->codigo, $ter->numero_identificacion, $ter->nombre_corto);
			$codeuda = (object)[
				"deudor" => $nom,
				"numeroObligacion" => $sc->numero_obligacion,
				"fechaInicio" => $sc->fecha_desembolso,
				"valorInicial" => $sc->valor_credito,
				"tasaMV" => $sc->tasa,
				"saldoCapital" => $sc->saldoObligacion($fechaConsulta),
				"calificacion" => $sc->calificacion_obligacion
			];
			$codeudas->push($codeuda);
		}

		$saldados = $obj->tercero
			->solicitudesCreditos()
			->where('fecha_desembolso', '<=', $fechaConsulta)
			->where('fecha_cancelación', '>=', $fechaConsulta->copy()->subYear())
			->estado('SALDADO')
			->get();

		return view('socios.socio.consultaCreditosLista')
			->withSocio($obj)
			->withCreditos($creditos)
			->withCodeudas($codeudas)
			->withSaldados($saldados)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaSocioAhorros(ModalidadAhorro $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la información del socio');
		$request->validate([
				'fecha'					=> 'bail|required|date_format:"d/m/Y"',
				'socio'					=> 'bail|required|exists:sqlsrv.socios.socios,id,deleted_at,NULL'
		]);
		$socio = Socio::find($request->socio);
		$this->objEntidad($socio->tercero);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $request->fecha)->endOfMonth()->startOfDay();
		$fechaInicial = clone $fechaConsulta;
		$fechaInicial->subMonths(36);

		$movimientos = $obj->movimientosAhorros()
			->socioId($socio->id)
			->whereBetween('fecha_movimiento', array($fechaInicial, $fechaConsulta))
			->orderBy('fecha_movimiento', 'desc')
			->get();

		return view('socios.socio.consultaAhorros')
			->withMovimientos($movimientos)
			->withSocio($socio)
			->withModalidad($obj)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaSocioCreditos(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la información del socio');
		$request->validate([
				'fecha'	=> 'bail|required|date_format:"d/m/Y"',
				'socio'	=> 'bail|required|exists:sqlsrv.socios.socios,id,deleted_at,NULL'
		]);

		$socio = Socio::find($request->socio);
		$this->objEntidad($socio->tercero);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $request->fecha)->endOfMonth()->startOfDay();

		$movimientos = collect([]);
		$res = DB::select('EXEC creditos.sp_movimientos_por_obligacion ?, ?', [$obj->id, $fechaConsulta]);
		$movimientos = collect($res);
		$movimientos->transform(function($item, $key) {
			$item->fecha_movimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $item->fecha_movimiento)->startOfDay();
			return $item;
		});
		$fechaAmortizacionUltimoPago = '';
		$amortizaciones = $obj->amortizaciones;
		if($amortizaciones->count()) {
			$fechaAmortizacionUltimoPago = $amortizaciones[$amortizaciones->count() - 1]->fecha_cuota;
		}
		$ultimoMovimiento = $obj->movimientosCapitalCredito()->orderBy('fecha_movimiento', 'desc')->first();
		$ultimoMovimiento = $ultimoMovimiento->fecha_movimiento;
		$codeudores = collect();
		foreach($obj->codeudores as $codeudor) {
			$tercero = $codeudor->tercero;
			$socioCodeudor = $tercero->socio;
			$data = ["nombre" => $tercero->nombre_completo, "socioId" => optional($socioCodeudor)->id, "estado" => is_null($socioCodeudor)? "No asociado" : $socioCodeudor->estado];
			$codeudores->push((object) $data);
		}
		return view('socios.socio.consultaCreditos')
			->withCredito($obj)
			->withSocio($socio)
			->withMovimientos($movimientos)
			->withFechaUltimoPago($fechaAmortizacionUltimoPago)
			->withUltimoMovimiento($ultimoMovimiento)
			->withCodeudores($codeudores)
			->withFechaConsulta($fechaConsulta);
	}

	public function consultaSocioRecaudos(Socio $obj, ControlProceso $objControlProceso, Request $request) {
		$this->objEntidad($obj->tercero, 'No está autorizado a ingresar a la información del socio');
		$request->validate([
				'fecha'	=> 'bail|required|date_format:"d/m/Y"',
		]);
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $request->fecha)->endOfMonth()->startOfDay();
		$recaudos = RecaudoNomina::whereTerceroId($obj->tercero->id)->whereControlProcesoId($objControlProceso->id)->get();
		$periodo = $objControlProceso->calendarioRecaudo->numero_periodo . '.' . $objControlProceso->calendarioRecaudo->fecha_recaudo;
		$periodicidad = $objControlProceso->calendarioRecaudo->pagaduria->periodicidad_pago;
		return view('socios.socio.consultaRecaudos')
			->withSocio($obj)
			->withPeriodo($periodo)
			->withPeriodicidad($periodicidad)
			->withRecaudos($recaudos)
			->withProceso($objControlProceso)
			->withFechaConsulta($fechaConsulta);
	}

	public function getObtenerPeriodicidadesPorModalidad(Request $request) {
		$request->validate([
			'modalidad' => [
							'bail',
							'required',
							'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $this->getEntidad()->id . ',esta_activa,1,deleted_at,NULL',
					]
		]);
		$modalidad = Modalidad::find($request->modalidad);
		return response()->json($modalidad->getPeriodicidadesDePagoAdmitidas());
	}

	public function simularCredito(Request $request) {
		Validator::make($request->all(), [
			'fechaConsulta'		=> 'bail|required|date_format:"d/m/Y"',
			'socio'				=> [
									'bail',
									'required',
									'exists:sqlsrv.socios.socios,id,deleted_at,NULL',
								],
			'modalidad'			=> [
									'bail',
									'required',
									'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $this->getEntidad()->id . ',esta_activa,1,deleted_at,NULL',
								],
			'valorCredito'		=> 'bail|required|integer|min:1',
			'plazo'				=> 'bail|required|integer|min:1|max:1000',
			'periodicidad'		=> 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO',
		], [
			'modalidad.required'		=> 'La :attribute es requerida',
			'modalidad.exists'			=> 'La :attribute seleccionada no es válida',
			'valorCredito.min'			=> 'El :attribute debe ser un valor válido',
			'periodicidad.required'		=> 'La :attribute es requerida',
			'plazo.max'					=> 'El :attribute de la simulación no es coherente, se espera el número de cuotas'
		], [
			'valorCredito'			=> 'valor de credito'
		])->validate();

		$socio = Socio::find($request->socio);
		if($socio->tercero->entidad_id != $this->getEntidad()->id) {
			return response()->json(['socio' => ['El socio no es válido']], 422);
		}
		$fechaCredito = Carbon::createFromFormat("d/m/Y", $request->fechaConsulta)->startOfDay();
		$modalidad = Modalidad::find($request->modalidad);
		$valorCredito = $request->valorCredito;
		$plazo = $request->plazo;
		$periodicidad = $request->periodicidad;
		$fechaPrimerPago = $socio->pagaduria->calendarioRecaudos()->whereEstado("programado")->first();
		$fechaPrimerPago = $fechaPrimerPago->fecha_recaudo;

		$amortizacion = FinancieroHelper::obtenerAmortizacion($modalidad, $valorCredito, $fechaCredito, $fechaPrimerPago, $plazo, $periodicidad);
		if(!$amortizacion) {
			return response()->json(["error" => "Plazo invalido"], 412);
		}
		$plazoTmp = ConversionHelper::conversionValorPeriodicidad($plazo, "MENSUAL", $periodicidad);
		$tasa = null;
		if($modalidad->es_tasa_condicionada) {
			$condicion = $modalidad->condicionesModalidad()->whereTipoCondicion("TASA")->first();
			if(!$condicion)return null;
			if(!$condicion->contenidoEnCondicion($plazoTmp))return null;
			$tasa = floatval($condicion->valorCondicionado($plazoTmp));
		}
		else {
			$tasa = $modalidad->tasa;
		}
		foreach ($amortizacion as &$elemento) {
			$elemento["fechaCuota"] = $elemento["fechaCuota"]->format("d/m/Y");
			$elemento["capital"] = number_format($elemento["capital"], 0);
			$elemento["intereses"] = number_format($elemento["intereses"], 0);
			$elemento["total"] = number_format($elemento["total"], 0);
			$elemento["nuevoSaldoCapital"] = number_format($elemento["nuevoSaldoCapital"], 0);
		}

		$data = array(
				"amortizacion" => $amortizacion,
				"fechaCredito" => $fechaCredito->format("d/m/Y"),
				"tasa" => number_format($tasa, 2)
		);

		return response()->json($data);
	}

	////API

	public function socio(Request $request) {
		if(!empty($request->q)) {
			$socios = Socio::entidad($request->entidad)->search($request->q)->limit(20)->get();
		}
		else {
			$socios = Socio::entidad($request->entidad)->take(20)->get();
		}

		$resultado = array('total_count' => $socios->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($socios as $socio) {
			$item = array('id' => $socio->id, 'text' => $socio->tercero->nombre_completo);
			array_push($resultado['items'], $item);
		}

		return response()->json($resultado);
	}

	public function documentacion(Socio $obj, Request $request) {
		$entidad = $this->getEntidad();
		$anioIc = $entidad->fecha_inicio_contabilidad->year;
		$ai = $anioIc > 2018 ? $anioIc : 2018; 
		$v = Validator::make($request->all(), [
			"certificado" => "bail|required|string|in:certificadoTributario",
			"anio" => "bail|required|integer|min:$ai|max:3000"
		]);
		if($v->fails()) {
			abort(401, "No se pudo procesar los datos (Año no válido)");
		}

		$pdf = null;
		switch ($request->certificado) {
			case 'certificadoTributario':
				$pdf = new CertificadoTributario($obj, $request->anio);
				break;
		}
		$pdf = $pdf->getRuta();
		$nombre = "Certificado tributario %s %s";
		$nombre = sprintf($nombre, $obj->tercero->numero_identificacion, $obj->tercero->nombre_corto);
		$nombre = Str::slug($nombre, "_") . ".pdf";
		return response()->file($pdf, ["Content-Disposition" => "filename=\"$nombre\""]);
	}

	public static function routes() {
		Route::get('socio', 'Socios\SocioController@index');
		Route::get('socio/create', 'Socios\SocioController@create');
		Route::post('socio', 'Socios\SocioController@store');

		Route::get('socio/{obj}/edit', 'Socios\SocioController@edit')->name('socioEdit');
		Route::put('socio/{obj}', 'Socios\SocioController@update');

		Route::get('socio/{obj}/laboral', 'Socios\SocioController@editLaboral')->name('socioEditLaboral');
		Route::put('socio/{obj}/laboral', 'Socios\SocioController@updateLaboral');

		Route::get('socio/{obj}/contacto', 'Socios\SocioController@editContacto')->name('socioEditContacto');
		Route::put('socio/{obj}/contacto', 'Socios\SocioController@updateContacto');

		Route::get('socio/{obj}/financiera', 'Socios\SocioController@editFinanciera')->name('socioEditFinanciera');
		Route::put('socio/{obj}/financiera', 'Socios\SocioController@updateFinanciera');

		Route::get('socio/{obj}/beneficiarios', 'Socios\SocioController@editBeneficiarios')->name('socioEditBeneficiarios');
		Route::put('socio/{obj}/beneficiarios', 'Socios\SocioController@updateBeneficiarios');
		Route::delete('socio/{obj}/beneficiarios', 'Socios\SocioController@deleteBeneficiarios')->name('socio.beneficiario.delete');

		Route::get('socio/{obj}/tarjetasCredito', 'Socios\SocioController@editTarjetasCredito')->name('socioEditTarjetasCredito');
		Route::put('socio/{obj}/tarjetasCredito', 'Socios\SocioController@updateTarjetasCredito');
		Route::get('socio/{obj}/tarjetasCredito/{tarjetaCredito}', 'Socios\SocioController@deleteTarjetasCredito')->name('socioEditTarjetasCreditoEliminar');

		Route::get('socio/{obj}/obligacionesFinancieras', 'Socios\SocioController@editObligacionesFinancieras')->name('socioEditObligacionesFinancieras');
		Route::put('socio/{obj}/obligacionesFinancieras', 'Socios\SocioController@updateObligacionesFinancieras');
		Route::get('socio/{obj}/obligacionesFinancieras/{obligacionFinanciera}', 'Socios\SocioController@deleteObligacionesFinancieras')->name('socioEditObligacionesFinancierasEliminar');;

		Route::get('socio/{obj}/imagenes', 'Socios\SocioController@editImagenes')->name('socioEditImagenes');
		Route::put('socio/{obj}/imagenes', 'Socios\SocioController@updateImagenes');

		Route::get('socio/{obj}/afiliacion', 'Socios\SocioController@editAfiliacion')->name('socioAfiliacion');
		Route::put('socio/{obj}/afiliacion', 'Socios\SocioController@updateAfiliacion');

		Route::get('socio/getSocio', 'Socios\SocioController@getSocio');
		Route::get('socio/getSocioConParametros', 'Socios\SocioController@getSocioConParametros');

		Route::get('socio/consulta', 'Socios\SocioController@consultaSocio');
		Route::get('socio/consulta/ahorros/{obj}/lista', 'Socios\SocioController@consultaAhorrosLista')->name('socio.consulta.ahorros.lista');
		Route::get('socio/consulta/ahorros/{obj}', 'Socios\SocioController@consultaSocioAhorros')->name('socio.consulta.ahorros');
		Route::get('socio/consulta/creditos/{obj}/lista', 'Socios\SocioController@consultaCreditosLista')->name('socio.consulta.creditos.lista');
		Route::get('socio/consulta/creditos/{obj}', 'Socios\SocioController@consultaSocioCreditos')->name('socioConsultaCreditos');
		Route::get('socio/consulta/recaudos/{obj}/lista', 'Socios\SocioController@consultaRecaudosLista')->name('socio.consulta.recaudos.lista');
		Route::get('socio/consulta/recaudos/{obj}/{objControlProceso}', 'Socios\SocioController@consultaSocioRecaudos')->name('socioConsultaRecaudos');
		Route::get('socio/consulta/simulador/{obj}', 'Socios\SocioController@consultaSocioSimulador')->name('socio.consulta.simulador');
		Route::get('socio/consulta/documentacion/{obj}', 'Socios\SocioController@consultaSocioDocumentacion')->name('socio.consulta.documentacion');
		
		Route::get('socio/consulta/obtenerPeriodicidadesPorModalidad', 'Socios\SocioController@getObtenerPeriodicidadesPorModalidad');
		Route::get('socio/consulta/simularCredito', 'Socios\SocioController@simularCredito');

		Route::get('socio/consulta/{obj}/documentacion', 'Socios\SocioController@documentacion')->name("socioConsulta.documentacion");
	}
}
