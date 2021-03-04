<?php

namespace App\Http\Controllers\Consulta;

use Route;
use Session;
use Validator;
use Carbon\Carbon;
use App\Api\Creditos;
use App\Traits\ICoreTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Models\Creditos\Modalidad;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\Recaudos\RecaudoNomina;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Recaudos\ControlProceso;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\TipoIdentificacion;
use App\Certificados\CertificadoTributario;
use App\Mail\Consulta\Perfil\PasswordUpdated;
use App\Models\General\ParametroInstitucional;
use App\Certificados\CertificadoExtractoSocial;
use App\Models\Reportes\ConfiguracionExtractoSocial;
use App\Events\Creditos\SolicitudCreditoDigitalEnviada;
use App\Http\Requests\Consulta\Perfil\EditPerfilRequest;
use App\Http\Requests\Consulta\Consulta\CreateSolicitudCreditoRequest;

class ConsultaController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:web');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a consulta de socios", $request);

		$socio = \Auth::user()->socios[0];
		$tercero = $socio->tercero;
		$genero = (object)["masculino" => true, "femenino" => false];
		if($tercero->sexo->codigo == 2) {
			//femenino
			$genero->masculino = false;
			$genero->femenino = true;
		}
		else {
			//masculino
			$genero->masculino = true;
			$genero->femenino = false;
		}
		return view('consulta.consulta.index')
			->withSocio($socio)
			->withGenero($genero);
	}

	public function ahorrosLista() {
		$this->log("Ingresó a lista de ahorros de la consulta del socio");

		$socio = \Auth::user()->socios[0];
		$tercero = $socio->tercero;

		$fechaConsulta = Carbon::now()->startOfDay();
		$ahorros = collect();
		$SDATs = collect();

		//Se obtiene los ahorros del socio
		$res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$socio->id, $fechaConsulta]);
		$ahorros = collect($res);
		$ahorros->transform(function($item, $key) {
			$item->cuotaMes = ConversionHelper::conversionValorPeriodicidad($item->cuota, $item->periodicidad, 'MENSUAL');
			if(!empty($item->vencimiento)) {
				$item->vencimiento = Carbon::createFromFormat('Y/m/d', $item->vencimiento)->startOfDay();
			}
			return $item;
		});

		foreach($socio->SDATs as $sdat) {
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

		return view('consulta.consulta.ahorrosLista')
			->withSocio($socio)
			->withAhorros($ahorros)
			->withSdats($SDATs)
			->withFechaConsulta($fechaConsulta);
	}

	public function ahorros(ModalidadAhorro $obj) {
		$this->log(sprintf("Ingresó a consultar los ahorros de la modalidad '%s'", $obj->nombre));
		$this->objEntidad($obj, 'No está autorizado a ingresar a la información');
		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();

		$fechaInicial = clone $fechaConsulta;
		$fechaInicial->subMonths(36);

		$movimientos = $obj->movimientosAhorros()
			->with(['movimiento', 'movimiento.tipoComprobante'])
			->socioId($socio->id)
			->whereBetween('fecha_movimiento', array($fechaInicial, $fechaConsulta))
			->orderBy('fecha_movimiento', 'desc')
			->get();

		return view('consulta.consulta.ahorros')
			->withMovimientos($movimientos)
			->withSocio($socio)
			->withModalidad($obj);
	}

	public function creditosLista() {
		$this->log("Ingresó a lista de créditos de la consulta del socio");
		$fechaConsulta = Carbon::now()->startOfDay();

		$socio = \Auth::user()->socios[0];
		$tercero = $socio->tercero;

		$creditos = collect();
		$codeudas = collect();
		$saldados = collect();

		$creditos = $tercero
			->solicitudesCreditos()
			->where('fecha_desembolso', '<=', $fechaConsulta)
			->estado('DESEMBOLSADO')
			->get();

		$creditos->transform(function($item, $key) use($fechaConsulta) {
			$item->saldoCapital = $item->saldoObligacion($fechaConsulta);
			$item->saldoIntereses = $item->saldoInteresObligacion($fechaConsulta);
			return $item;
		});

		$cod = $tercero->codeudas()->whereHas('solicitudCredito', function($q){
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

		$saldados = $tercero
			->solicitudesCreditos()
			->where('fecha_desembolso', '<=', $fechaConsulta)
			->where('fecha_cancelación', '>=', $fechaConsulta->copy()->subYear())
			->estado('SALDADO')
			->get();

		return view('consulta.consulta.creditosLista')
			->withSocio($socio)
			->withCreditos($creditos)
			->withCodeudas($codeudas)
			->withSaldados($saldados)
			->withFechaConsulta($fechaConsulta);
	}

	public function creditos(SolicitudCredito $obj) {
		$this->log(sprintf("Ingresó a consultar el crédito '%s'", $obj->numero_obligacion));
		$socio = \Auth::user()->socios[0];
		$fechaConsulta = Carbon::now()->endOfMonth()->startOfDay();

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
		return view('consulta.consulta.creditos')
			->withCredito($obj)
			->withSocio($socio)
			->withMovimientos($movimientos)
			->withFechaUltimoPago($fechaAmortizacionUltimoPago)
			->withUltimoMovimiento($ultimoMovimiento)
			->withCodeudores($codeudores);
	}

	public function recaudosLista() {
		$this->log("Ingresó a lista de recaudos de la consulta del socio");
		$fechaConsulta = Carbon::now()->startOfDay();

		$socio = \Auth::user()->socios[0];
		$tercero = $socio->tercero;

		$recaudos = collect();
		$recaudos = $tercero
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

		return view('consulta.consulta.recaudosLista')
			->withSocio($socio)
			->withRecaudos($recaudos)
			->withFechaConsulta($fechaConsulta);
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
			'modalidad' => [
				'bail',
				'required',
				'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $this->getEntidad()->id . ',esta_activa,1,deleted_at,NULL',
			],
			'valorCredito' => 'bail|required|integer|min:1',
			'plazo' => 'bail|required|integer|min:1|max:1000',
			'periodicidad' => 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO',
		],
		[
			'modalidad.required' => 'La :attribute es requerida',
			'modalidad.exists' => 'La :attribute seleccionada no es válida',
			'valorCredito.min' => 'El :attribute debe ser un valor válido',
			'periodicidad.required' => 'La :attribute es requerida',
			'plazo.max' => 'El :attribute de la simulación no es coherente, se espera el número de cuotas'
		],
		[
			'valorCredito' => 'valor de credito'
		])->validate();

		$socio = \Auth::user()->socios[0];
		$fechaCredito = Carbon::now()->startOfDay();
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
		$porcentajeMaximoEndeudamientoPermitido = 0;
		$tipos = TipoIdentificacion::activo()->aplicacion('NATURAL')->orderBy('nombre')->get()->pluck('nombre', 'id');
		$usuario = \Auth::user();
		$socio = $usuario->socios[0];
		$tercero = $socio->tercero;
		$porcentajeMaximoEndeudamientoPermitido = ParametroInstitucional::entidadId($this->getEntidad()->id)->codigo('CR003')->first();
		$porcentajeMaximoEndeudamientoPermitido = empty($porcentajeMaximoEndeudamientoPermitido) ? 100 : $porcentajeMaximoEndeudamientoPermitido->valor;
		$recaudoAplicado = $socio->pagaduria->calendarioRecaudos()
			->whereHas('controlProceso', function($query){
				$query->where('estado', 'APLICADO')
					->orWhere('estado', 'AJUSTADO');
			})
			->where('estado', 'EJECUTADO')
			->orderBy('fecha_recaudo', 'desc')
			->first();
		return view('consulta.perfil.index')
			->withUsuario($usuario)
			->withSocio($socio)
			->withTercero($tercero)
			->withRecaudoAplicado($recaudoAplicado)
			->withFecha(Carbon::now()->startOfDay())
			->withPorcentajeMaximoEndeudamientoPermitido($porcentajeMaximoEndeudamientoPermitido);
	}

	public function perfilEditar() {
		$this->log("Ingresó a edición del perfil");
		$usuario = \Auth::user();
		$socio = $usuario->socios[0];
		$tercero = $socio->tercero;
		return view('consulta.perfil.editar')->withUsuario($usuario)->withSocio($socio)->withTercero($tercero);
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

	public function consultaDocumentacion() {
		$this->log("Ingresó a lista de documentación de la consulta del socio");
		$fechaConsulta = Carbon::now()->startOfDay();
		$socio = \Auth::user()->socios[0];

		$entidadId = $socio->tercero->entidad_id;
		$fecha = Carbon::now();
		$extractosSociales = ConfiguracionExtractoSocial::entidadId($entidadId)
			->activosParaSocios($fecha)
			->orderBy("anio", "desc")
			->get();

		return view('consulta.consulta.consultaDocumentacion')
			->withSocio($socio)
			->withFechaConsulta($fechaConsulta)
			->withExtractosSociales($extractosSociales);
	}

	public function documentacionTributario(Request $request) {
		$entidad = $this->getEntidad();
		$socio = \Auth::user()->socios[0];
		$anioIc = $entidad->fecha_inicio_contabilidad->year;
		$ai = $anioIc > 2018 ? $anioIc : 2018;
		$v = Validator::make($request->all(), [
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
		$this->log("Descargó de documentacion 'Certificado tributario'");

		$pdf = new CertificadoTributario($socio, $request->anio);
		$pdf = $pdf->getRuta();
		$nombre = "Certificado tributario %s %s";
		$nombre = sprintf($nombre, $socio->tercero->numero_identificacion, $socio->tercero->nombre_corto);
		$nombre = Str::slug($nombre, "_") . ".pdf";
		return response()->file($pdf, ["Content-Disposition" => "filename=\"$nombre\""]);
	}

	public function documentacionExtractoSocial(Request $request)
	{
	    $entidad = $this->getEntidad();
		$socio = \Auth::user()->socios[0];
		$v = Validator::make($request->all(), [
			"anio" => [
				"bail",
				"required",
				"integer",
				"min:2010",
				"max:3000",
				"exists:sqlsrv.reportes.configuraciones_extracto_social,anio,entidad_id," . $entidad->id . ",deleted_at,NULL"
			]
		]);
		if($v->fails()) {
			abort(401, "No se pudo procesar los datos (Año no válido)");
		}
		$this->log("Descargó de documentacion 'Extracto Social'");

		$pdf = new CertificadoExtractoSocial($socio, $request->anio);
		$pdf = $pdf->getRuta();
		$nombre = "Extracto Social %s %s";
		$nombre = sprintf($nombre, $socio->tercero->numero_identificacion, $socio->tercero->nombre_corto);
		$nombre = Str::slug($nombre, "_") . ".pdf";
		return response()->file($pdf, ["Content-Disposition" => "filename=\"$nombre\""]);
	}

	public function simular() {
		$this->log("Ingresó al simulador de crédito de la consulta del socio");

		$modalidadesCredito = Modalidad::entidadId()->activa()->usoSocio()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad) {
			if($modalidad->estaParametrizada() == false) {
				continue;
			}
			$modalidades[$modalidad->id] = $modalidad->nombre;
		}

		return view('consulta.consulta.simulador')
			->withModalidades($modalidades);
	}

	public function solicitarCredito(Request $request)
	{
		$this->log("Ingresó a solicitar crédito en la consulta del socio");

		$modalidadesCredito = Modalidad::entidadId()->activa()->usoSocio()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad) {
			if($modalidad->estaParametrizada() == false) {
				continue;
			}
			$modalidades[$modalidad->id] = $modalidad->nombre;
		}

		$cupoDisponible = 0;
		$socio = \Auth::user()->socios[0];
		$cupoDisponible = $socio
			->tercero
			->cupoDisponible(Carbon::now()->startOfDay());

		$cupoDisponible = $cupoDisponible < 0 ? 0 : $cupoDisponible;

		return view('consulta.consulta.solicitarCredito')
			->withModalidades($modalidades)
			->withCupoDisponible($cupoDisponible);
	}

	public function crearSolicitarCredito(CreateSolicitudCreditoRequest $request)
	{
		$msg = "Ingresó a crear crédito en la consulta del socio con los siguientes parametros: '%s'";
		$msg = sprintf($msg, json_encode($request->all()));
		$this->log($msg, 'CREAR');

		$socio = \Auth::user()->socios[0];
		$solicitud = Creditos::crearSolicitudCredito(
			$socio,
			$request->modalidad,
			$request->valorCredito,
			$request->plazo,
			$request->observaciones
		);

		event(new SolicitudCreditoDigitalEnviada($solicitud->id, $socio->id));

		Session::flash("message", "Se ha enviado con exito la solicitud de crédito");

		return view("consulta.consulta.solicitarCreditoConfirmacion")
			->withSocio($socio)
			->withSolicitudCredito($solicitud);
	}

	public static function routes() {
		Route::get('consulta', 'Consulta\ConsultaController@index');
		Route::get('consulta/ahorros/lista', 'Consulta\ConsultaController@ahorrosLista');
		Route::get('consulta/ahorros/{obj}', 'Consulta\ConsultaController@ahorros')->name('consulta.ahorros');
		Route::get('consulta/creditos/lista', 'Consulta\ConsultaController@creditosLista');
		Route::get('consulta/creditos/{obj}', 'Consulta\ConsultaController@creditos')->name('consulta.creditos');
		Route::get('consulta/recaudos/lista', 'Consulta\ConsultaController@recaudosLista');
		Route::get('consulta/recaudos/{obj}', 'Consulta\ConsultaController@recaudos')->name('consulta.recaudos');

		Route::get('consulta/obtenerPeriodicidadesPorModalidad', 'Consulta\ConsultaController@getObtenerPeriodicidadesPorModalidad');
		Route::get('consulta/simulador', 'Consulta\ConsultaController@simular');
		Route::get('consulta/simularCredito', 'Consulta\ConsultaController@simularCredito');

		Route::get('consulta/perfil', 'Consulta\ConsultaController@perfil');
		Route::get('consulta/perfil/editar', 'Consulta\ConsultaController@perfilEditar');
		Route::put('consulta/perfil', 'Consulta\ConsultaController@perfilUpdate');

		Route::get('consulta/documentacion', 'Consulta\ConsultaController@consultaDocumentacion')->name('consulta.documentacion');
		Route::get('consulta/documentacion/tritutario', 'Consulta\ConsultaController@documentacionTributario');
		Route::get('consulta/documentacion/extractoSocial', 'Consulta\ConsultaController@documentacionExtractoSocial');

		Route::get('consulta/solicitarCredito', 'Consulta\ConsultaController@solicitarCredito');
		Route::post('consulta/solicitarCredito', 'Consulta\ConsultaController@crearSolicitarCredito');
	}
}
