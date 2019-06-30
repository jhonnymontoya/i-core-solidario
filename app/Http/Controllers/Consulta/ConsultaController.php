<?php

namespace App\Http\Controllers\Consulta;

use App\Certificados\CertificadoTributario;
use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consulta\Perfil\EditPerfilRequest;
use App\Mail\Consulta\Perfil\PasswordUpdated;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Models\General\TipoIdentificacion;
use App\Models\Recaudos\ControlProceso;
use App\Models\Recaudos\RecaudoNomina;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Route;
use Validator;
use Session;

class ConsultaController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:web');
		//$this->middleware('verEnt');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a consulta de socios", $request);
		$entidad = $this->getEntidad();
		
		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();

		$modalidadesCredito = Modalidad::entidadId()->activa()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad)$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;

		$ahorros = collect();
		$creditos = collect();
		$recaudos = collect();
		$SDATs = collect();
		$porcentajeMaximoEndeudamientoPermitido = 0;
		$recaudoAplicado = null;

		//Se obtiene los ahorros del socio
		$res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$socio->id, $fechaConsulta]);
		$ahorros = collect($res);
		$ahorros->transform(function($item, $key) {
			if(!empty($item->vencimiento)) {
				$item->vencimiento = Carbon::createFromFormat('Y/m/d', $item->vencimiento)->startOfDay();
			}
			return $item;
		});

		$recaudos = $socio->tercero->recaudosNomina()
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

		$porcentajeMaximoEndeudamientoPermitido = ParametroInstitucional::entidadId($this->getEntidad()->id)
												->codigo('CR003')
												->first();
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

		//SDATs
		foreach($socio->SDATs as $sdat) {
			$rendimientos = $sdat->rendimientosSdat()
				->where("fecha_movimiento", '<=', $fechaConsulta)
				->get();
			$deposito = (object)[
				"id" => $sdat->id,
				"codigo" => $sdat->tipoSDAT->codigo,
				"valor" => '$' . number_format($sdat->valor),
				"fecha_constitucion" => $sdat->fecha_constitucion,
				"plazo" => $sdat->plazo,
				"fecha_vencimiento" => $sdat->fecha_vencimiento,
				"tasa" => number_format($sdat->tasa, 2) . '%',
				"estado" => $sdat->estado,
				"rendimientos" => '$' . number_format($rendimientos->sum("valor"))
			];
			$SDATs->push($deposito);
		}

		return view('consulta.consulta.index')
			->withSocio($socio)
			->withAhorros($ahorros)
			->withRecaudos($recaudos)
			->withModalidades($modalidades)
			->withRecaudoAplicado($recaudoAplicado)
			->withPorcentajeMaximoEndeudamientoPermitido($porcentajeMaximoEndeudamientoPermitido)
			->withSdats($SDATs);
	}

	public function ahorros(ModalidadAhorro $obj) {
		$this->log(sprintf("Ingresó a consultar los ahorros de la modalidad '%s'", $obj->nombre));
		$this->objEntidad($obj, 'No está autorizado a ingresar a la información');
		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();

		$fechaInicial = clone $fechaConsulta;
		$fechaInicial->subMonths(36);

		$movimientos = $obj->movimientosAhorros()
							->socioId($socio->id)
							->whereBetween('fecha_movimiento', array($fechaInicial, $fechaConsulta))
							->orderBy('fecha_movimiento', 'desc')
							->get();

		return view('consulta.consulta.ahorros')
			->withMovimientos($movimientos)
			->withSocio($socio)
			->withModalidad($obj);
	}

	public function creditos(SolicitudCredito $obj) {
		$this->log(sprintf("Ingresó a consultar el crédito '%s'", $obj->numero_obligacion));
		$this->objEntidad($obj, 'No está autorizado a ingresar a la información');

		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();

		$movimientos = collect([]);
		$res = DB::select('EXEC creditos.sp_movimientos_por_obligacion ?, ?', [$obj->id, $fechaConsulta]);
		$movimientos = collect($res);
		$movimientos->transform(function($item, $key) {
			$item->fecha_movimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $item->fecha_movimiento)->startOfDay();
			return $item;
		});
		return view('consulta.consulta.creditos')
			->withCredito($obj)
			->withSocio($socio)
			->withMovimientos($movimientos);
	}

	public function recaudos(ControlProceso $obj) {
		$this->log("Ingresó a consultar recaudos");

		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();
		$recaudos = RecaudoNomina::whereTerceroId($socio->tercero->id)->whereControlProcesoId($obj->id)->get();
		$periodo = $obj->calendarioRecaudo->numero_periodo . '.' . $obj->calendarioRecaudo->fecha_recaudo;
		$periodicidad = $obj->calendarioRecaudo->pagaduria->periodicidad_pago;
		return view('consulta.consulta.recaudos')
			->withSocio($socio)
			->withPeriodo($periodo)
			->withPeriodicidad($periodicidad)
			->withRecaudos($recaudos)
			->withProceso($obj);
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

		$socio = \Auth::user()->socios[0];
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

	/**
	 * Muestra el formulario para editar el usuario
	 * @return type
	 */
	public function perfil() {
		$this->log("Ingresó al perfil");
		$tipos = TipoIdentificacion::activo()->aplicacion('NATURAL')->orderBy('nombre')->get()->pluck('nombre', 'id');
		$usuario = \Auth::user();
		$socio = $usuario->socios[0];
		$tercero = $socio->tercero;
		return view('consulta.perfil.index')
			->withUsuario($usuario)
			->withSocio($socio)
			->withTercero($tercero)
			->withSocio($socio);
	}

	/**
	 * Guarda los datos del usuario
	 * @return type
	 */
	public function perfilUpdate(EditPerfilRequest $request) {
		$usuario = \Auth::user();
		$socio = $usuario->socios[0];
		$tercero = $socio->tercero;
		//avatar,password
		
		if(!empty($request->password)) {
			$usuario->password = bcrypt($request->password);
			$this->log("Actualizó la contraseña", "ACTUALIZAR");
			$usuario->save();
		}

		if(!empty($request->avatar)) {
			$this->log("Actualizó el avatar", "ACTUALIZAR");
			$socio->avatar = $request->avatar;
			$socio->save();
		}

		$contacto = $tercero->getContacto(true, true);
		if(!empty($contacto) && !empty($contacto->email)) {
			if(!empty($request->password)) {
				Mail::to($contacto->email)->send(new PasswordUpdated($usuario));
			}
		}
		Session::flash('message', 'Se ha actualizado el perfil');
		return redirect('consulta/perfil');
	}

	public function documentacion(Request $request) {
		$entidad = $this->getEntidad();
		$socio = \Auth::user()->socios[0];
		$anioIc = $entidad->fecha_inicio_contabilidad->year;
		$ai = $anioIc > 2018 ? $anioIc : 2018; 
		$v = Validator::make($request->all(), [
			"certificado" => [
				"bail",
				"required",
				"string",
				"in:certificadoTributario"
			],
			"anio" => [
				"bail",
				"required",
				"integer",
				"min:" . $ai,
				"max:3000"
			]
		]);
		if($v->fails()) {
			abort(401, "No se pudo procesar los datos (Año no válido)");
		}
		$this->log(sprintf("Descargó de documentacion '%s'", $request->certificado));

		$pdf = null;
		switch ($request->certificado) {
			case 'certificadoTributario':
				$pdf = new CertificadoTributario($socio, $request->anio);
				break;
		}
		$pdf = $pdf->getRuta();
		$nombre = "Certificado tributario %s %s";
		$nombre = sprintf($nombre, $socio->tercero->numero_identificacion, $socio->tercero->nombre_corto);
		$nombre = str_slug($nombre, "_") . ".pdf";
		return response()->file($pdf, ["Content-Disposition" => "filename=\"$nombre\""]);
	}

	public static function routes() {
		Route::get('consulta', 'Consulta\ConsultaController@index');
		Route::get('consulta/ahorros/{obj}', 'Consulta\ConsultaController@ahorros')->name('consulta.ahorros');
		Route::get('consulta/creditos/{obj}', 'Consulta\ConsultaController@creditos')->name('consulta.creditos');
		Route::get('consulta/recaudos/{obj}', 'Consulta\ConsultaController@recaudos')->name('consulta.recaudos');

		Route::get('consulta/obtenerPeriodicidadesPorModalidad', 'Consulta\ConsultaController@getObtenerPeriodicidadesPorModalidad');
		Route::get('consulta/simularCredito', 'Consulta\ConsultaController@simularCredito');

		Route::get('consulta/perfil', 'Consulta\ConsultaController@perfil');
		Route::put('consulta/perfil', 'Consulta\ConsultaController@perfilUpdate');

		Route::get('consulta/documentacion', 'Consulta\ConsultaController@documentacion')->name("consulta.documentacion");
	}
}
