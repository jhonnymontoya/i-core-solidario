<?php

namespace App\Http\Controllers\Creditos;

use App\Helpers\ConversionHelper;
use App\Http\Controllers\Controller;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Http\Requests\Creditos\SolicitudCredito\AprobarSolicitudCreditoRequest;
use App\Http\Requests\Creditos\SolicitudCredito\DesembolsarSolicitudCreditoRequest;
use App\Http\Requests\Creditos\SolicitudCredito\EditConsolidacionSaldoRequest;
use App\Http\Requests\Creditos\SolicitudCredito\EditSolicitudCreditoRequest;
use App\Http\Requests\Creditos\SolicitudCredito\MakeCuotaExtraordinariaRequest;
use App\Http\Requests\Creditos\SolicitudCredito\ValidarSolicitudCreditoRequest;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\Codeudor;
use App\Models\Creditos\CumplimientoCondicion;
use App\Models\Creditos\CuotaExtraordinaria;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\ObligacionConsolidacion;
use App\Models\Creditos\ParametroContable;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Creditos\TipoGarantia;
use App\Models\General\ParametroInstitucional;
use App\Models\General\Tercero;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Route;
use Validator;

class SolicitudCreditoController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	/**
	 * Devuelve las solicitudes de crédito con filtros para el index
	 * @param Request $request 
	 * @return type
	 */
	public function index(Request $request) {
		$this->logActividad("Ingreso a solicitud de créditos", $request);
		Validator::make($request->all(), [
			'inicio'	=> 'bail|nullable|date_format:"d/m/Y"',
			'fin'		=> 'bail|nullable|date_format:"d/m/Y"',
			'modalidad'	=> [
							'bail',
							'nullable',
							'exists:sqlsrv.creditos.modalidades,id,deleted_at,NULL',
						],
			'estado'	=> 'bail|nullable|string|in:SALDADO,DESEMBOLSADO,APROBADO,ANULADO,RECHAZADO,RADICADO,BORRADOR',
		])->validate();

		$existenFiltros = false;
		$solicitudesCreditos = SolicitudCredito::entidadId();
		if(!empty($request->name)) {
			$solicitudesCreditos->search($request->name);
			$existenFiltros = true;
		}
		if(!empty($request->inicio)) {
			$solicitudesCreditos->where('fecha_solicitud', '>=', Carbon::createFromFormat('d/m/Y', $request->inicio)->startOfDay());
			$existenFiltros = true;
		}
		if(!empty($request->fin)) {
			$solicitudesCreditos->where('fecha_solicitud', '<=', Carbon::createFromFormat('d/m/Y', $request->fin)->endOfDay());
			$existenFiltros = true;
		}
		if(!empty($request->modalidad)) {
			$solicitudesCreditos->whereModalidadCreditoId($request->modalidad);
			$existenFiltros = true;
		}
		if(!empty($request->estado)) {
			$solicitudesCreditos->whereEstadoSolicitud($request->estado);
			$existenFiltros = true;
		}
		if(!$existenFiltros)$solicitudesCreditos->whereIn('estado_solicitud', ['BORRADOR', 'RADICADO', 'APROBADO']);

		$solicitudesCreditos = $solicitudesCreditos->orderBy('fecha_solicitud', 'desc')->paginate();
		$modalidadesCredito = Modalidad::entidadId()->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad)$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;

		$estados = [
			'BORRADOR'			=> 'Borrador',
			'RADICADO'			=> 'Radicado',
			'APROBADO'			=> 'Aprobado',
			'DESEMBOLSADO'		=> 'Desembolsado',
			'SALDADO'			=> 'Saldado',
			'ANULADO'			=> 'Anulado',
			'RECHAZADO'			=> 'Rechazado',
		];

		return view('creditos.solicitudCredito.index')
						->withSolicitudes($solicitudesCreditos)
						->withModalidades($modalidades)
						->withEstados($estados);
	}

	public function create() {
		$this->log("Ingresó a crear una solicitud de crédito", 'INGRESAR');
		$modalidadesCredito = Modalidad::entidadId()->activa()->usoParaTarjeta(false)->get();
		$modalidades = array();
		foreach($modalidadesCredito as $modalidad)$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;		
		return view('creditos.solicitudCredito.create')->withModalidades($modalidades);
	}

	public function store(ValidarSolicitudCreditoRequest $request) {
		$this->log("Creó una solicitud de crédito con los siguientes parámetros " . json_encode($request->all()), 'CREAR');
		$modalidadesCredito = Modalidad::find($request->modalidad);
		$tercero = Tercero::find($request->solicitante);
		$fechaSolicitud = Carbon::createFromFormat('d/m/Y', $request->fecha_solicitud)->startOfDay();
		$seguroCartera = null;
		if($modalidadesCredito->segurosCartera->count() > 0) {
			$seguroCartera = $modalidadesCredito->segurosCartera[0];
		}

		$solicitud = new SolicitudCredito;
		$solicitud->entidad_id = $this->getEntidad()->id;
		$solicitud->tercero_id = $tercero->id;
		$solicitud->modalidad_credito_id = $modalidadesCredito->id;
		$solicitud->seguro_cartera_id = optional($seguroCartera)->id;

		$res = DB::select(
			'select creditos.fn_cupo_por_modalidad(?, ?, ?) AS cupo_modalidad',
			[$modalidadesCredito->id, $tercero->id, $fechaSolicitud]
		);
		$valor = count($res) ? intval($res[0]->cupo_modalidad) : 0;

		$solicitud->valor_solicitud = $valor < 0 ? 0 : $valor;
		$solicitud->valor_credito = $valor < 0 ? 0 : $valor;
		$solicitud->fecha_solicitud = $fechaSolicitud;
		$solicitud->tipo_pago_intereses = $modalidadesCredito->pago_interes;
		$solicitud->tipo_amortizacion = $modalidadesCredito->tipo_cuota;
		$solicitud->tipo_tasa = $modalidadesCredito->tipo_tasa;
		$solicitud->tasa = $modalidadesCredito->obtenerValorTasa(
			$valor,
			0,
			$solicitud->fecha_solicitud,
			$tercero->socio->fecha_ingreso,
			$tercero->socio->fecha_antiguedad
		);
		$solicitud->aplica_mora = $modalidadesCredito->aplica_mora;
		$solicitud->save();

		//Se agregan los documentos
		$solicitud->documentos()->sync($solicitud->modalidadCredito->documentacionModalidad->pluck('id'));
		return redirect()->route('solicitudCreditoEdit', $solicitud);
	}

	public function edit(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'BORRADOR') {
			Session::flash('error', 'No es posible editar una solicitud de crédito con un estado diferente a BORRADOR');
			return redirect('solicitudCredito');
		}

		$listaProgramaciones = $this->listaProgramaciones($obj);
		return view('creditos.solicitudCredito.edit')
				->withSolicitud($obj)
				->withPeriodicidades($obj->modalidadCredito->getPeriodicidadesDePagoAdmitidas())
				->withProgramaciones($listaProgramaciones);
	}

	public function update(EditSolicitudCreditoRequest $request, SolicitudCredito $obj)	{
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		$this->actualizar($obj, $request);
		$condiciones = $obj->modalidadCredito->condicionesModalidad;
		foreach($condiciones as $condicion) {
			switch($condicion->condicionado_por) {
				case "PLAZO":
					$plazoTmp = ConversionHelper::conversionValorPeriodicidad($obj->plazo, 'MENSUAL', $request->periodicidad);
					$plazoTmp = ceil($plazoTmp);
					if(!$condicion->contenidoEnCondicion($plazoTmp)) {
						Session::flash('error', 'El plazo de la solicitud no está contenido dentro de los parámetros de la modalidad');
						return redirect()->route('solicitudCreditoEdit', $obj);
					}
					break;
				case "MONTO":
					if(!$condicion->contenidoEnCondicion($obj->valor_credito)) {
						Session::flash('error', 'El monto solicitado no está contenido dentro de los parámetros de la modalidad');
						return redirect()->route('solicitudCreditoEdit', $obj);
					}
					break;
				case "ANTIGUEDADENTIDAD":
					$socio = optional($obj->tercero)->socio;
					$meses = 0;
					if($socio) {
						if($socio->estado == 'ACTIVO') {
							$meses = $obj->fecha_solicitud->diffInMonths($socio->fecha_antiguedad);
						}
					}
					if(!$condicion->contenidoEnCondicion($meses)) {
						Session::flash('error', 'La antigüedad del socio no está contenida dentro de los parámetros de la modalidad');
						return redirect()->route('solicitudCreditoEdit', $obj);
					}
					break;
				case "ANTIGUEDADEMPRESA":
					$socio = optional($obj->tercero)->socio;
					$meses = 0;
					if($socio) {
						if($socio->estado == 'ACTIVO') {
							$meses = $obj->fecha_solicitud->diffInMonths($socio->fecha_ingreso);
						}
					}
					if(!$condicion->contenidoEnCondicion($meses)) {
						Session::flash('error', 'La antigüedad en la empresa del socio no está contenida dentro de los parámetros de la modalidad');
						return redirect()->route('solicitudCreditoEdit', $obj);
					}
					break;
				default:
					break;
			}
		}
		$obj->save();
		$res = DB::select('exec creditos.sp_amortizacion_credito ?', [$obj->id]);
		if ($res) {
			if ($res[0]->ERROR == 1) {
				Session::flash('error', $res[0]->MENSAJE);
				return redirect()->route('solicitudCreditoEdit', $obj);
			}
		}
		return redirect()->route('solicitudCreditoEdit', $obj);		
	}

	public function getTasaCondicionada(Request $request, SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		Validator::make($request->all(), [
			'valor'			=> 'required|integer|min:1',
			'periodicidad'	=> 'required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL'
		])->validate();

		$valor = ConversionHelper::conversionValorPeriodicidad($request->valor, 'MENSUAL', $request->periodicidad);
		$valor = ceil($valor);

		$tasa = 0;
		if($obj->modalidadCredito->es_tasa_condicionada) {
			$condicion = $obj->modalidadCredito->condicionesModalidad()->whereTipoCondicion('TASA')->first();
			if(!empty($condicion)) {
				$tasa = number_format($condicion->valorCondicionado($valor), 2);
			}
		}		
		return response()->json(["tasa" => $tasa]);
	}

	public function radicar(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if(!$obj->estado_solicitud == 'BORRADOR' && !$obj->amortizaciones->count()) {
			return redirect()->route('solicitudCreditoEdit', $obj->id);
		}
		$obj->estado_solicitud = 'RADICADO';
		$obj->save();

		//Se sincronizan los documentos
		$obj->documentos()->sync($obj->modalidadCredito->documentacionModalidad->pluck('id'));
		$this->validarCondiciones($obj);
		Session::flash('message', 'Se ha radicado la solicitud de crédito, ahora puede continuar con las siguientes etapas');
		return redirect('solicitudCredito');
	}

	public function aprobar(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'RADICADO') {
			Session::flash('error', 'No es posible aprobar una solicitud de crédito con un estado diferente a RADICADO');
			return redirect('solicitudCredito');
		}

		$listaProgramaciones = $this->listaProgramaciones($obj);
		return view('creditos.solicitudCredito.aprobar')
				->withSolicitud($obj)
				->withPeriodicidades($obj->modalidadCredito->getPeriodicidadesDePagoAdmitidas())
				->withProgramaciones($listaProgramaciones);
	}

	private function obtenerPlazoParametro($obj) {
		$modalidad = $obj->modalidadCredito;
		if($modalidad->es_plazo_condicionado) {
			$condicion = $modalidad->condicionesModalidad()->whereTipoCondicion('PLAZO')->first();
			if(empty($condicion))return 0;
			switch($condicion->condicionado_por) {
				case 'ANTIGUEDADEMPRESA':
					return empty($obj->tercero->socio) ? 0 : $this->socioAntiguedadEmpresa($obj, $condicion);
					break;
				case 'ANTIGUEDADENTIDAD':
					return empty($obj->tercero->socio) ? 0 : number_format($this->socioAntiguedadEntidad($obj, $condicion), 0);
					break;
				case 'MONTO':
					return number_format($condicion->valorCondicionado($obj->valor_credito), 0);
					break;
				
				default:
					return 0;
					break;
			}
		}
		else {
			return $modalidad->plazo;
		}
	}

	private function socioAntiguedadEntidad($obj, $condicion) {
		$antiguedad = $obj->tercero->socio->fecha_antiguedad;
		if(empty($antiguedad))return 0;
		$antiguedad = $obj->fecha_solicitud->diffInMonths($antiguedad, true);
		return $condicion->valorCondicionado($antiguedad);
	}

	private function socioAntiguedadEmpresa($obj, $condicion) {
		$antiguedad = $obj->tercero->socio->fecha_ingreso;
		if(empty($antiguedad))return 0;
		$antiguedad = $obj->fecha_solicitud->diffInMonths($antiguedad, true);
		return $condicion->valorCondicionado($antiguedad);
	}

	private function obtenerMontoParametro($obj) {
		$modalidad = $obj->modalidadCredito;
		if($modalidad->es_monto_condicionado) {
			$condicion = $modalidad->condicionesModalidad()->whereTipoCondicion('MONTO')->first();
			if(empty($condicion))return 0;
			$plazoSolicitud = 0;
			switch($obj->periodicidad) {
				case 'ANUAL':$plazoSolicitud = ($obj->plazo * 12); break;
				case 'SEMESTRAL':$plazoSolicitud = ($obj->plazo * 6); break;
				case 'CUATRIMESTRAL':$plazoSolicitud = ($obj->plazo * 4); break;
				case 'TRIMESTRAL':$plazoSolicitud = ($obj->plazo * 3); break;
				case 'BIMESTRAL':$plazoSolicitud = ($obj->plazo * 2); break;
				case 'MENSUAL':$plazoSolicitud = $obj->plazo; break;
				case 'QUINCENAL':$plazoSolicitud = ($obj->plazo * 0.5); break;
				case 'CATORCENAL':$plazoSolicitud = ($obj->plazo * (12 / 26)); break;
				case 'DECADAL':$plazoSolicitud = ($obj->plazo * (12 / 36)); break;
				case 'SEMANAL':$plazoSolicitud = ($obj->plazo * (12 / 52)); break;
			}
			switch($condicion->condicionado_por) {
				case 'PLAZO':
					return $condicion->valorCondicionado($plazoSolicitud);
					break;
				case 'ANTIGUEDADEMPRESA':
					return empty($obj->tercero->socio) ? 0 : $this->socioAntiguedadEmpresa($obj, $condicion);
					break;
				case 'ANTIGUEDADENTIDAD':
					return empty($obj->tercero->socio) ? 0 : $this->socioAntiguedadEntidad($obj, $condicion);
					break;
				case 'MONTO':
					return $condicion->valorCondicionado($obj->valor_credito);
					break;				
				default:
					return 0;
					break;
			}
		}
		elseif($modalidad->es_monto_cupo) {
			return $obj->tercero->cupoDisponible('31/12/2100');
		}
		else {
			return $obj->modalidadCredito->monto;
		}
	}

	public function alternarCondicion(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		Validator::make($request->all(), [
			'condicion' => 'required|exists:sqlsrv.creditos.cumplimiento_condiciones,id,solicitud_id,' . $obj->id,
		])->validate();
		$condicion = CumplimientoCondicion::find($request->condicion);

		if(empty($condicion))
			return response()->json(["ok" => false, "mensaje" => "no se encontró la condición"], 400);

		if($condicion->es_aprobada) {
			$condicion->es_aprobada = false;
			$condicion->aprobado_por_usuario_id = null;
		}
		else {
			$condicion->es_aprobada = true;
			$condicion->aprobado_por_usuario_id = $this->getUser()->id;
		}
		$condicion->save();
		return response()->json(["ok" => true, "estado" => $condicion->es_aprobada]);
	}

	public function alternarDocumento(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		Validator::make($request->all(), ['documento' => 'required',])->validate();
		
		$cumple = $obj->documentos->where('id', $request->documento)->first()->pivot->cumple;
		$cumple = !$cumple;
		$obj->documentos()->updateExistingPivot($request->documento, ['cumple' => $cumple]);

		$numeroDocumentos = $obj->documentos()->whereObligatorio('1')->count();
		$documentosCumplidos = $obj->documentos()->whereObligatorio('1')->wherePivot('cumple', 1)->count();
		$cumplimientoDocumento = $obj->cumplimientoCondiciones()->whereCondicion('Documentación')->first();
		$cumplimientoDocumento->valor_parametro = $numeroDocumentos;
		$cumplimientoDocumento->valor_solicitud = $documentosCumplidos;
		$cumplimientoDocumento->cumple_parametro = $cumplimientoDocumento->valor_parametro == $cumplimientoDocumento->valor_solicitud ? 1 : 0;
		$cumplimientoDocumento->es_aprobada = null;
		$cumplimientoDocumento->aprobado_por_usuario_id = null;
		$cumplimientoDocumento->save();
		return response()->json(["ok" => true, "estado" => $cumple]);
	}

	public function anular(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'BORRADOR' && $obj->estado_solicitud != 'RADICADO' && $obj->estado_solicitud != 'APROBADO') {
			Session::flash('error', 'No es posible anular la solicitud de crédito ya que se encuentra en estado ' . $obj->estado_solicitud);
			return redirect('solicitudCredito');
		}
		return view('creditos.solicitudCredito.anular')->withSolicitud($obj);
	}

	public function anularUpdate(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'BORRADOR' && $obj->estado_solicitud != 'RADICADO' && $obj->estado_solicitud != 'APROBADO') {
			Session::flash('error', 'No es posible anular la solicitud de crédito ya que se encuentra en estado ' . $obj->estado_solicitud);
			return redirect('solicitudCredito');
		}

		$obj->estado_solicitud = 'ANULADO';
		$obj->save();

		//$obj->movimientosCapitalCredito()->forceDelete();
		$obj->amortizaciones()->forceDelete();
		$obj->cumplimientoCondiciones()->forceDelete();
		$obj->documentos()->detach();

		Session::flash('message', 'Se ha anulado la solicitud de crédito');
		return redirect('solicitudCredito');
	}

	public function rechazar(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'RADICADO') {
			Session::flash('error', 'No es posible rechazar la solicitud de crédito ya que se encuentra en estado ' . $obj->estado_solicitud);
			return redirect('solicitudCredito');
		}
		return view('creditos.solicitudCredito.rechazar')->withSolicitud($obj);
	}

	public function rechazarUpdate(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		Validator::make($request->all(), [
			'observaciones' => 'bail|required|string|min:5|max:1000',
		], [
			'observaciones.required' 	=> 'Las :attribute son requeridas',
			'observaciones.min'			=> 'Las :attribute deben tener al menos :min caracteres',
			'observaciones.max'			=> 'Las :attribute no deben tener más de :max caracteres',
		])->validate();
		if($obj->estado_solicitud != 'RADICADO') {
			Session::flash('error', 'No es posible rechazar la solicitud de crédito ya que se encuentra en estado ' . $obj->estado_solicitud);
			return redirect('solicitudCredito');
		}

		$obj->estado_solicitud = 'RECHAZADO';
		$obj->observaciones = $request->observaciones;
		$obj->save();

		//$obj->movimientosCapitalCredito()->forceDelete();
		$obj->amortizaciones()->forceDelete();
		$obj->cumplimientoCondiciones()->forceDelete();
		$obj->documentos()->detach();

		Session::flash('message', 'Se ha anulado la solicitud de crédito');
		return redirect('solicitudCredito');
	}

	public function aprobarUpdate(SolicitudCredito $obj, AprobarSolicitudCreditoRequest $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		$this->actualizar($obj, $request);
		$obj->fecha_aprobacion = $request->fecha_aprobacion;
		$obj->estado_solicitud = 'APROBADO';
		$obj->save();
		DB::statement('exec creditos.sp_amortizacion_credito ?', [$obj->id]);

		//Se sincronizan los documentos
		$obj->documentos()->sync($obj->modalidadCredito->documentacionModalidad->pluck('id'));
		$this->validarCondiciones($obj);

		Session::flash('message', 'Se ha aprobado la solicitud de crédito, ahora puede continuar con las siguientes etapas');
		return redirect('solicitudCredito');
	}

	public function desembolsar(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'APROBADO') {
			Session::flash('error', 'No es posible desembolsar una solicitud de crédito con un estado diferente a APROBADO');
			return redirect('solicitudCredito');
		}

		$listaProgramaciones = $this->listaProgramaciones($obj);
		return view('creditos.solicitudCredito.desembolsar')
				->withSolicitud($obj)
				->withPeriodicidades($obj->modalidadCredito->getPeriodicidadesDePagoAdmitidas())
				->withProgramaciones($listaProgramaciones);
	}

	public function desembolsarUpdate(SolicitudCredito $obj, DesembolsarSolicitudCreditoRequest $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'APROBADO') {
			Session::flash('error', 'No es posible desembolsar una solicitud de crédito con un estado diferente a APROBADO');
			return redirect('solicitudCredito');
		}
		$this->actualizar($obj, $request);
		$obj->fecha_desembolso = $request->fecha_desembolso;
		$obj->estado_solicitud = 'APROBADO';
		$obj->save();
		DB::statement('exec creditos.sp_amortizacion_credito ?', [$obj->id]);

		$liquidacion = $this->obtenerCobroAdministrativo($obj);

		//Se sincronizan los documentos
		$obj->documentos()->sync($obj->modalidadCredito->documentacionModalidad->pluck('id'));
		$this->validarCondiciones($obj);
		return view('creditos.solicitudCredito.confirmarDesembolso')->withSolicitud($obj)->withLiquidacion($liquidacion);
	}

	public function obtenerCobroAdministrativo($solicitudDeCredito) {
		$liquidacion = collect();
		$cobroAdministrativo = $solicitudDeCredito->modalidadCredito->cobrosAdministrativos;
		$cobroAdministrativo = $cobroAdministrativo->count() ? $cobroAdministrativo[0] : null;

		$valorFinalCredito = $solicitudDeCredito->valor_credito;
		$valorCreditoRecogidos = 0;
		if(!is_null($cobroAdministrativo)) {
			$valorCobro = $cobroAdministrativo->calculoValorCobro($solicitudDeCredito);
		}
		else {
			$valorCobro = 0;
		}

		foreach($solicitudDeCredito->obligacionesConsolidadas as $solicitud)$valorCreditoRecogidos += $solicitud->total;
		$desembolso = $valorFinalCredito - $valorCreditoRecogidos;
		if(!is_null($cobroAdministrativo)) {
			if($cobroAdministrativo->efecto == 'DEDUCCIONCREDITO') {
				$desembolso -= $valorCobro;
			}
			else{
				if(!is_null($cobroAdministrativo)) {
					$cobro = $cobroAdministrativo->calculoBaseCobro($solicitudDeCredito);
					if(is_numeric($cobro)) {
						$valorCobro = 0;
					}
					elseif($cobro->factorCalculo == 'PORCENTAJEBASE') {
						$valorFinalCredito = (100 * $valorFinalCredito) / (100 - $cobro->valor);
						$valorCobro = ($cobro->valor * $valorFinalCredito) / 100;
					}
					else {
						$valorFinalCredito += $cobro->valor;
						$valorCobro = $cobro->valor;
					}
				}
			}
		}
		$liquidacion->cobroAdministrativo = $cobroAdministrativo;
		$liquidacion->nombreCobro = optional($cobroAdministrativo)->nombre;
		$liquidacion->valorCobro = $valorCobro;
		$liquidacion->valorFinalCredito = $valorFinalCredito;
		$liquidacion->desembolso = $desembolso;
		$liquidacion->valorCreditoRecogidos = $valorCreditoRecogidos;
		return $liquidacion;
	}

	private function actualizar($obj, $request) {
		if($obj->estado_solicitud == 'BORRADOR') $obj->valor_solicitud = $request->valor_credito;
		$obj->valor_credito = $request->valor_credito;
		$obj->plazo = $request->plazo;
		$obj->forma_pago = $request->forma_pago;
		$obj->periodicidad = $request->periodicidad;
		$obj->fecha_primer_pago = $request->fecha_primer_pago;
		$obj->observaciones = $request->observaciones;

		if($obj->modalidadCredito->tipo_cuota == 'CAPITAL') {
			$obj->fecha_primer_pago_intereses = $request->fecha_primer_pago_intereses;
		}

		if($obj->modalidadCredito->es_tasa_condicionada) {
			$condicion = $obj->modalidadCredito->condicionesModalidad()->whereTipoCondicion('TASA')->first();

			if(!empty($condicion)) {
				if($condicion->condicionado_por == 'MONTO' || $condicion->condicionado_por == 'PLAZO') {
					$obj->tasa = $request->tasa;
				}
			}
		}
	}

	private function validarCondiciones($obj) {
		/*CONDICIONES*/
		$plazoSolicitud = 0;
		$plazoParametro = $this->obtenerPlazoParametro($obj);
		switch($obj->periodicidad) {
			case 'ANUAL':$plazoSolicitud = ($obj->plazo * 12); break;
			case 'SEMESTRAL':$plazoSolicitud = ($obj->plazo * 6); break;
			case 'CUATRIMESTRAL':$plazoSolicitud = ($obj->plazo * 4); break;
			case 'TRIMESTRAL':$plazoSolicitud = ($obj->plazo * 3); break;
			case 'BIMESTRAL':$plazoSolicitud = ($obj->plazo * 2); break;
			case 'MENSUAL':$plazoSolicitud = $obj->plazo; break;
			case 'QUINCENAL':$plazoSolicitud = ($obj->plazo * 0.5); break;
			case 'CATORCENAL':$plazoSolicitud = ($obj->plazo * (12 / 26)); break;
			case 'DECADAL':$plazoSolicitud = ($obj->plazo * (12 / 36)); break;
			case 'SEMANAL':$plazoSolicitud = ($obj->plazo * (12 / 52)); break;
		}

		$montoParametro = $this->obtenerMontoParametro($obj);
		$validacionCondiciones = [
			[
				'condicion' => 'Plazo',
				'valor_parametro' => $plazoParametro,
				'valor_solicitud' => $plazoSolicitud,
				'cumple_parametro' => ($plazoParametro >= $plazoSolicitud),
			],
			[
				'condicion' => 'Monto',
				'valor_parametro' => intval($montoParametro),
				'valor_solicitud' => $obj->valor_credito,
				'cumple_parametro' => ($montoParametro >= $obj->valor_credito),
			],
		];

		if($obj->modalidadCredito->afecta_cupo) {
			$cupo = $obj->tercero->cupoDisponible('31/12/2100');
			array_push($validacionCondiciones, [
				'condicion' => 'Cupo',
				'valor_parametro' => $cupo,
				'valor_solicitud' => $obj->valor_credito,
				'cumple_parametro' => ($cupo >= $obj->valor_credito),
			]);
		}

		if(!empty($obj->modalidadCredito->minimo_antiguedad_entidad)) {
			$antiguedad = $obj->tercero->socio->fecha_antiguedad;
			if(empty($antiguedad))$antiguedad = 0;
			if($obj->estado_solicitud == "RADICADO") {
				$antiguedad = $obj->fecha_solicitud->diffInMonths($antiguedad, true);
			}
			else {
				$antiguedad = $obj->fecha_aprobacion->diffInMonths($antiguedad, true);
			}
			array_push($validacionCondiciones, [
				'condicion' => 'Antigüedad entidad',
				'valor_parametro' => $obj->modalidadCredito->minimo_antiguedad_entidad,
				'valor_solicitud' => $antiguedad,
				'cumple_parametro' => ($obj->modalidadCredito->minimo_antiguedad_entidad <= $antiguedad),
			]);
		}

		if(!empty($obj->modalidadCredito->minimo_antiguedad_empresa)) {
			$antiguedad = $obj->tercero->socio->fecha_ingreso;
			if(empty($antiguedad))$antiguedad = 0;
			if($obj->estado_solicitud == "RADICADO") {
				$antiguedad = $obj->fecha_solicitud->diffInMonths($antiguedad, true);
			}
			else {
				$antiguedad = $obj->fecha_aprobacion->diffInMonths($antiguedad, true);
			}
			array_push($validacionCondiciones, [
				'condicion' => 'Antigüedad empresa',
				'valor_parametro' => $obj->modalidadCredito->minimo_antiguedad_empresa,
				'valor_solicitud' => $antiguedad,
				'cumple_parametro' => ($obj->modalidadCredito->minimo_antiguedad_empresa <= $antiguedad),
			]);
		}

		if(!empty($obj->modalidadCredito->limite_obligaciones)) {
			$obligacionesActivas = $obj->tercero->solicitudesCreditos()->whereModalidadCreditoId($obj->id)->estado('DESEMBOLSADO')->count();
			array_push($validacionCondiciones, [
				'condicion' => 'Limite obligaciones',
				'valor_parametro' => $obj->modalidadCredito->limite_obligaciones,
				'valor_solicitud' => $obligacionesActivas,
				'cumple_parametro' => ($obj->modalidadCredito->limite_obligaciones >= $obligacionesActivas),
			]);
		}

		if(!empty($obj->modalidadCredito->intervalo_solicitudes)) {
			$intervalo = $obj->tercero->solicitudesCreditos()->whereModalidadCreditoId($obj->id)->estado('DESEMBOLSADO')->count();
			if($intervalo == 0) {
				$intervalo = $obj->tercero->solicitudesCreditos()->whereModalidadCreditoId($obj->id)->estado('SALDADO')->orderBy('fecha_cancelacion', 'desc')->first();
				if(empty($intervalo)) $intervalo = $obj->modalidadCredito->intervalo_solicitudes;
				else $intervalo = $intervalo->fecha_cancelacion->diffInMonths($obj->fecha_aprobacion, true);
			}
			else {
				$intervalo = $obj->modalidadCredito->intervalo_solicitudes;
			}

			array_push($validacionCondiciones, [
				'condicion' => 'Intervalo solicitudes',
				'valor_parametro' => $obj->modalidadCredito->intervalo_solicitudes,
				'valor_solicitud' => $intervalo,
				'cumple_parametro' => ($obj->modalidadCredito->intervalo_solicitudes >= $intervalo),
			]);
		}

		$endeudamientoValorParametro = optional(ParametroInstitucional::entidadId()->codigo('CR003')->first())->valor;
		$endeudamientoValorsolicitud = $obj->tercero->socio->endeudamientoEstudioSolicitud($obj->fecha_solicitud, $obj->id);
		array_push($validacionCondiciones, [
			'condicion' => 'Endeudamiento',
			'valor_parametro' =>  $endeudamientoValorParametro,
			'valor_solicitud' => $endeudamientoValorsolicitud,
			'cumple_parametro' => ($endeudamientoValorsolicitud <= $endeudamientoValorParametro) ? true : false
		]);

		$garantias = $obj->getCodeudoresGarantias();
		array_push($validacionCondiciones, [
			'condicion' => 'Garantías',
			'valor_parametro' =>  $garantias['valorParametro'],
			'valor_solicitud' => $garantias['valorSolicitud'],
			'cumple_parametro' => $garantias['cumple']
		]);

		$numeroDocumentos = $obj->documentos()->whereObligatorio('1')->count();
		$documentosCumplidos = $obj->documentos()->whereObligatorio('1')->wherePivot('cumple', 1)->count();
		array_push($validacionCondiciones, [
			'condicion' => 'Documentación',
			'valor_parametro' => $numeroDocumentos,
			'valor_solicitud' => $documentosCumplidos,
			'cumple_parametro' => $numeroDocumentos == $documentosCumplidos ? true : false
		]);

		foreach($validacionCondiciones as &$condicion) {
			$parametroCondicion = $obj->cumplimientoCondiciones()->whereCondicion($condicion['condicion'])->first();
			if(empty($parametroCondicion))continue;
			if(!$condicion['cumple_parametro']) {
				if($parametroCondicion->es_aprobada) {
					$condicion['es_aprobada'] = 1;
					$condicion['aprobado_por_usuario_id'] = $parametroCondicion->aprobado_por_usuario_id;
				}
			}
		}
		foreach($validacionCondiciones as &$valor) {
			if(!empty($valor['valor_parametro'])) $valor['valor_parametro'] = ceil($valor['valor_parametro']);
			if(!empty($valor['valor_solicitud'])) $valor['valor_solicitud'] = ceil($valor['valor_solicitud']);
		}
		$obj->cumplimientoCondiciones()->forceDelete();
		$obj->cumplimientoCondiciones()->createMany($validacionCondiciones);
	}

	public function procesar(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->estado_solicitud != 'APROBADO') {
			Session::flash('error', 'No es posible procesar una solicitud de crédito con un estado diferente a APROBADO');
			return redirect('solicitudCredito');
		}
		$validator = Validator::make($request->all(), [
			'metodo'		=> 'bail|required|string|in:tercero,contable,tesoreria',
			'cuenta'		=> 'bail|required|exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $this->getEntidad()->id . ',esta_activo,1',
			'tercero'		=> 'bail|nullable|required_if:metodo,tercero|exists:sqlsrv.general.terceros,id,entidad_id,' . $this->getEntidad()->id . ',esta_activo,1',
		],[
			'cuenta.required'	=> 'La :attribute es requerida',
		]);

		if($validator->fails()) {
			Session::flash('error', 'Error seleccionando la cuenta o el tercero de desembolso');
			return redirect('solicitudCredito');
		}

		if($obj->obligacionesConsolidadas->count()) {
			$obligacionesRecogidas = $obj->obligacionesConsolidadas;
			$totalConsolidado = 0;
			foreach ($obligacionesRecogidas as $obligacion) {
				if($obligacion->creditoConsolidado->estado_solicitud != 'DESEMBOLSADO') {
					Session::flash('error', 'Error consolidando obligación, El crédito ' . $obligacion->creditoConsolidado->numero_obligacion . ' se encuentra en estado diferente a desembolsado');
					return redirect()->route('solicitudCreditoDesembolsar', $obj->id);
				}
				$saldoObligacion = $obligacion->creditoConsolidado->saldoObligacion('01/01/3000');
				if($saldoObligacion <= 0) {
					Session::flash('error', 'Error consolidando obligación, El crédito ' . $obligacion->creditoConsolidado->numero_obligacion . ' no tiene saldo para recoger');
					return redirect()->route('solicitudCreditoDesembolsar', $obj->id);
				}
				if($saldoObligacion < $obligacion->pago_capital) {
					Session::flash('error', 'Error consolidando obligación, El crédito ' . $obligacion->creditoConsolidado->numero_obligacion . ' no puede quedar con saldo negativo');
					return redirect()->route('solicitudCreditoDesembolsar', $obj->id);
				}
				$totalConsolidado += $obligacion->total;
			}
			if($totalConsolidado > $obj->valor_credito) {
				Session::flash('error', 'Error consolidando obligación, El valor de la solicitud no cubre los créditos recogidos');
				return redirect()->route('solicitudCreditoDesembolsar', $obj->id);
			}
		}

		//Se busca la cuenta de parametrización de cartera
		$cuenta = ParametroContable::entidadId()->tipoCartera('CONSUMO');
		$cuenta = $cuenta->tipoGarantia($obj->forma_pago == 'CAJA' ? 'OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA' : 'OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
		$cuenta = $cuenta->categoriaClasificacion('A')->first();

		if($cuenta == null) {
			Session::flash('error', 'No se encontró parametrización de clasificación contable para créditos.');
			return redirect('solicitudCredito');
		}

		//Se busca el tipo de comprobante, si este no existe o no es de uso 'PROCESO', se muestra
		//un error
		$tipoComprobante = TipoComprobante::uso('PROCESO')->entidadId();
		switch($request->metodo) {
			case 'tesoreria':
				$tipoComprobante = $tipoComprobante->whereCodigo('DCTS');
				break;
			case 'contable':
				$tipoComprobante = $tipoComprobante->whereCodigo('DCCO');
				break;
			case 'tercero':
				$tipoComprobante = $tipoComprobante->whereCodigo('DCOT');
				break;			
			default:
				break;
		}

		$tipoComprobante = $tipoComprobante->first();
		if($tipoComprobante == null) {
			Session::flash('error', 'No se encuentra el tipo de comprobante para el desembolso seleccionado.');
			return redirect('solicitudCredito');
		}

		$terceroContrapartida = null;
		if($request->metodo == 'tercero') {
			$terceroContrapartida = Tercero::find($request->tercero);
			if($terceroContrapartida == null) {
				Session::flash('error', 'No se encuentra el tercero para desembolso.');
				return redirect('solicitudCredito');
			}
		}

		$cuentaContrapartida = Cuif::find($request->cuenta);
		if($cuentaContrapartida == null) {
			Session::flash('error', 'No se encuentra la cuenta para desembolso.');
			return redirect('solicitudCredito');
		}

		//Se inicia transaccion
		DB::beginTransaction();
		try {

			$liquidacion = $this->obtenerCobroAdministrativo($obj);
			if(!is_null($liquidacion->cobroAdministrativo)) {
				$obj->valor_credito = $liquidacion->valorFinalCredito;
				$obj->save();
				DB::statement('exec creditos.sp_amortizacion_credito ?', [$obj->id]);
				//Se sincronizan los documentos
				$obj->documentos()->sync($obj->modalidadCredito->documentacionModalidad->pluck('id'));
				$this->validarCondiciones($obj);
			}

			//Se crea el movimiento temporal
			$movimientoTemporal = new MovimientoTemporal;

			$movimientoTemporal->entidad_id = $this->getEntidad()->id;
			$movimientoTemporal->tipo_comprobante_id = $tipoComprobante->id;
			$movimientoTemporal->descripcion = "Desembolso de crédito para '" . $obj->tercero->nombre_completo . "' bajo la modalidad '" . $obj->modalidadCredito->codigo . ' - ' . $obj->modalidadCredito->nombre . "'";
			$movimientoTemporal->fecha_movimiento = $obj->fecha_desembolso;
			$movimientoTemporal->origen = 'PROCESO';

			//Se guarda el movimiento temporal
			$movimientoTemporal->save();

			$detalles = collect();

			//Se crean los detalles del movimiento temporal
			$ajuste = new DetalleMovimientoTemporal;

			$ajuste->entidad_id = $this->getEntidad()->id;
			$ajuste->codigo_comprobante = $tipoComprobante->codigo;
			$ajuste->tercero_id = $obj->tercero->id;
			$ajuste->tercero_identificacion = $obj->tercero->numero_identificacion;
			$ajuste->tercero = $obj->tercero->nombre;
			$ajuste->cuif_id = $cuenta->cuentaCapital->id;
			$ajuste->cuif_codigo = $cuenta->cuentaCapital->codigo;
			$ajuste->cuif_nombre = $cuenta->cuentaCapital->nombre;
			$ajuste->serie = 1;
			$ajuste->fecha_movimiento = $obj->fecha_desembolso;
			$ajuste->credito = 0;
			$ajuste->debito = $obj->valor_credito;
			$detalles->push($ajuste);

			$valorContrapartida = $obj->valor_credito;

			if($obj->obligacionesConsolidadas->count()) {
				$obligacionesRecogidas = $obj->obligacionesConsolidadas;
				foreach ($obligacionesRecogidas as $obligacion) {
					$valorContrapartida -= $obligacion->total;
					$cuentaDetalle = ParametroContable::entidadId()->tipoCartera('CONSUMO');
					$cuentaDetalle = $cuentaDetalle->tipoGarantia($obligacion->creditoConsolidado->forma_pago == 'CAJA' ? 'OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA' : 'OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
					$cuentaDetalle = $cuentaDetalle->categoriaClasificacion($obligacion->creditoConsolidado->calificacion_obligacion)->first();

					$detalle = new DetalleMovimientoTemporal;
					$detalle->entidad_id = $this->getEntidad()->id;
					$detalle->codigo_comprobante = $tipoComprobante->codigo;
					$detalle->tercero_id = $obj->tercero->id;
					$detalle->tercero_identificacion = $obj->tercero->numero_identificacion;
					$detalle->tercero = $obj->tercero->nombre;
					$detalle->cuif_id = $cuentaDetalle->cuentaCapital->id;
					$detalle->cuif_codigo = $cuentaDetalle->cuentaCapital->codigo;
					$detalle->cuif_nombre = $cuentaDetalle->cuentaCapital->nombre;
					$detalle->serie = $detalles->count() + 1;
					$detalle->fecha_movimiento = $obj->fecha_desembolso;
					$detalle->credito = $obligacion->pago_capital;
					$detalle->debito = 0;
					$detalle->referencia = $obligacion->creditoConsolidado->numero_obligacion;
					$detalles->push($detalle);

					if($obligacion->pago_intereses != 0) {
						$detalle = new DetalleMovimientoTemporal;
						$detalle->entidad_id = $this->getEntidad()->id;
						$detalle->codigo_comprobante = $tipoComprobante->codigo;
						$detalle->tercero_id = $obj->tercero->id;
						$detalle->tercero_identificacion = $obj->tercero->numero_identificacion;
						$detalle->tercero = $obj->tercero->nombre;
						$detalle->cuif_id = $cuentaDetalle->cuentaInteresesIngreso->id;
						$detalle->cuif_codigo = $cuentaDetalle->cuentaInteresesIngreso->codigo;
						$detalle->cuif_nombre = $cuentaDetalle->cuentaInteresesIngreso->nombre;
						$detalle->serie = $detalles->count() + 1;
						$detalle->fecha_movimiento = $obj->fecha_desembolso;
						if($obligacion->pago_intereses > 0) {
							$detalle->credito = $obligacion->pago_intereses;
							$detalle->debito = 0;
						}
						else {
							$detalle->credito = 0;
							$detalle->debito = -$obligacion->pago_intereses;
						}
						$detalle->referencia = $obligacion->creditoConsolidado->numero_obligacion;
						$detalles->push($detalle);
					}
				}
			}

			if(!is_null($liquidacion->cobroAdministrativo) && $liquidacion->valorCobro != 0) {
				$cuenta = $liquidacion->cobroAdministrativo->cuentaDestino;
				//Cobro Administrativo
				$cobro = new DetalleMovimientoTemporal;

				$cobro->entidad_id = $this->getEntidad()->id;
				$cobro->codigo_comprobante = $tipoComprobante->codigo;
				$cobro->tercero_id = $obj->tercero->id;
				$cobro->tercero_identificacion = $obj->tercero->numero_identificacion;
				$cobro->tercero = $obj->tercero->nombre;
				$cobro->cuif_id = $cuenta->id;
				$cobro->cuif_codigo = $cuenta->codigo;
				$cobro->cuif_nombre = $cuenta->nombre;
				$cobro->serie = 1;
				$cobro->fecha_movimiento = $obj->fecha_desembolso;
				$cobro->credito = round($liquidacion->valorCobro, 0);
				$cobro->debito = 0;
				$detalles->push($cobro);

				$valorContrapartida -= round($liquidacion->valorCobro, 0);
			}

			if($valorContrapartida > 0) {
				//Se crean los detalles del movimiento temporal
				$ajusteContrapartida = new DetalleMovimientoTemporal;

				$ajusteContrapartida->entidad_id = $this->getEntidad()->id;
				$ajusteContrapartida->codigo_comprobante = $tipoComprobante->codigo;
				if($request->metodo == 'tercero') {
					$ajusteContrapartida->tercero_id = $terceroContrapartida->id;
					$ajusteContrapartida->tercero_identificacion = $terceroContrapartida->numero_identificacion;
					$ajusteContrapartida->tercero = $terceroContrapartida->nombre;
				}
				else {
					$ajusteContrapartida->tercero_id = $obj->tercero->id;
					$ajusteContrapartida->tercero_identificacion = $obj->tercero->numero_identificacion;
					$ajusteContrapartida->tercero = $obj->tercero->nombre;
				}
				$ajusteContrapartida->cuif_id = $cuentaContrapartida->id;
				$ajusteContrapartida->cuif_codigo = $cuentaContrapartida->codigo;
				$ajusteContrapartida->cuif_nombre = $cuentaContrapartida->nombre;
				$ajusteContrapartida->serie = $detalles->count() + 1;
				$ajusteContrapartida->fecha_movimiento = $obj->fecha_desembolso;
				$ajusteContrapartida->credito = $valorContrapartida;
				$ajusteContrapartida->debito = 0;
				$detalles->push($ajusteContrapartida);
			}
			$serie = 1;
			$detalles->each(function($item, $key) use(&$serie){
				$item->serie = $serie++;
			});
			$movimientoTemporal->detalleMovimientos()->saveMany($detalles);
			$respuesta = DB::select('exec creditos.sp_contabilizar_desembolso_credito ?, ?', [$movimientoTemporal->id, $obj->id]);		
			if($respuesta[0]->ERROR == '0') {
				if($this->getEntidad()->usa_tarjeta) {
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
		return redirect('solicitudCredito');
	}

	/**
	 * Retorna al formulario de garantías para la solicitud de crédito
	 * @return type
	 */
	public function garantias(SolicitudCredito $obj) {
		$this->objEntidad($obj);
		$tiposCodeudores = $obj->modalidadCredito->tiposGarantias->pluck('nombre', 'id');
		$data = $obj->getCodeudores($obj);
		return view('creditos.solicitudCredito.garantias')->withSolicitud($obj)->withTiposCodeudores($tiposCodeudores)->withData($data);
	}

	public function postCodeudor(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj);
		$reglas = [
			'tipo'		=> [
							'bail',
							'nullable',
							'exists:sqlsrv.creditos.tipos_garantia,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL',
						],
			'codeudor'	=> [
							'bail',
							'required',
							'integer',
							'exists:sqlsrv.general.terceros,id,entidad_id,' . $this->getEntidad()->id . ',tipo_tercero,NATURAL,deleted_at,NULL',
							'unique:sqlsrv.creditos.codeudores,tercero_id,NULL,id,solicitud_credito_id,' . $obj->id . ',deleted_at,NULL'
						]
		];
		if($obj->tercero_id == $request->codeudor)array_push($reglas['codeudor'], "regex:/^$/");
		$request->validate($reglas, ['codeudor.regex' => 'El deudor no puede ser codeudor']);

		$codeudor = new Codeudor;
		$tercero = Tercero::find($request->codeudor);
		$tipoGarantia = empty($request->tipo) ? null : TipoGarantia::find($request->tipo);

		$codeudor->solicitud_credito_id = $obj->id;
		$codeudor->tipo_garantia_id = optional($tipoGarantia)->id;
		$codeudor->tercero_id = $tercero->id;

		$codeudor->es_permanente = optional($tipoGarantia)->es_permanente;
		$codeudor->es_permanente_con_descubierto = optional($tipoGarantia)->es_permanente_con_descubierto;
		$codeudor->requiere_garantia_por_monto = optional($tipoGarantia)->requiere_garantia_por_monto;
		$codeudor->requiere_garantia_por_valor_descubierto = optional($tipoGarantia)->requiere_garantia_por_valor_descubierto;
		$codeudor->valor_parametro_monto = optional($tipoGarantia)->monto;
		$codeudor->valor_parametro_descubierto = optional($tipoGarantia)->valor_descubierto;

		$netosSaldos = 0;
		$fechaConsulta = empty($obj->fecha_solicitud) ? Carbon::now()->startOfDay(): $obj->fecha_solicitud->copy()->startOfDay();
		if($obj->tercero->socio) {
			$socio = $obj->tercero->socio;
			$totalAhorros = 0;
			$totalCreditos = 0;
			$ahorros = collect(DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$socio->id, $fechaConsulta]));
			foreach($ahorros as $ahorro)$totalAhorros += $ahorro->saldo;
			$creditos = $obj->tercero->solicitudesCreditos()->where('fecha_desembolso', '<=', $fechaConsulta)->estado('DESEMBOLSADO')->get();
			foreach($creditos as $credito)$totalCreditos += $credito->saldoObligacion($fechaConsulta);
			$netosSaldos = $totalAhorros - $totalCreditos;
		}
		$netosSaldos -= $obj->valor_credito;
		$codeudor->valor_descubierto = $netosSaldos > 0 ? 0 : -$netosSaldos;

		$codeudor->admite_codeudor_externo = optional($tipoGarantia)->admite_codeudor_externo;
		$codeudor->es_codeudor_externo = empty($tercero->socio) ? true : ($tercero->socio->estado != 'ACTIVO' ? true : false );
		$codeudor->valida_cupo_codeudor = optional($tipoGarantia)->valida_cupo_codeudor;
		$codeudor->cupo_codeudor = $tercero->cupoDisponible();
		$codeudor->tiene_limite_obligaciones_codeudor = optional($tipoGarantia)->tiene_limite_obligaciones_codeudor;
		$codeudor->parametro_limite_obligaciones_codeudor = optional($tipoGarantia)->limite_obligaciones_codeudor;
		$codeudor->numero_obligaciones_codeudor = $tercero->codeudas()->wherehas('solicitudCredito', function($q){$q->whereEstadoSolicitud('DESEMBOLSADO');})->count();
		$codeudor->tiene_limite_saldo_codeudas = optional($tipoGarantia)->tiene_limite_saldo_codeudas;
		$codeudor->parametro_limite_saldo_codeudas = optional($tipoGarantia)->limite_saldo_codeudas;

		$valorCodeudas = 0;
		$codeudas = $tercero->codeudas()->wherehas('solicitudCredito', function($q){$q->whereEstadoSolicitud('DESEMBOLSADO');})->get();
		foreach ($codeudas as $codeuda) {
			$valorCodeudas += $codeuda->solicitudCredito->saldoObligacion($fechaConsulta);
		}
		$codeudor->valor_saldo_codeudas = $valorCodeudas;
		$codeudor->valida_antiguedad_codeudor = optional($tipoGarantia)->valida_antiguedad_codeudor;
		$codeudor->parametro_antiguedad_codeudor = optional($tipoGarantia)->antiguedad_codeudor;
		$codeudor->valor_antiguedad_codeudor = empty($tercero->socio) ? 0 : $tercero->socio->fecha_antiguedad->diffInDays(Carbon::now()->startOfDay());
		$codeudor->valida_calificacion_codeudor = optional($tipoGarantia)->valida_calificacion_codeudor;
		$codeudor->parametro_calificacion_minima_requerida_codeudor = optional($tipoGarantia)->calificacion_minima_requerida_codeudor;
		$codeudor->valor_calificacion_codeudor = 'A';

		$codeudor->save();
		Session::flash('message', 'Se ha agregado el codeudor \'' . $tercero->nombre_corto . '\'');
		return redirect()->route('solicitudCreditoGarantias', $obj->id);
	}

	public function deleteCodeudor(SolicitudCredito $obj, Codeudor $codeudor) {
		$this->objEntidad($obj);
		if($codeudor->solicitud_credito_id != $obj->id) {
			Session::flash('error', 'No es posible eliminar el codeudor');
			return redirect()->route('solicitudCreditoGarantias', $obj->id);
		}
		$codeudor->delete();
		Session::flash('message', 'Se ha eliminado el codeudor \'' . $codeudor->tercero->nombre_corto . '\'');
		return redirect()->route('solicitudCreditoGarantias', $obj->id);
	}

	public function consolidacion(SolicitudCredito $obj) {
		$this->objEntidad($obj);
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO,BORRADOR')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		$creditosVigentes = $obj->tercero->solicitudesCreditos()->where('fecha_desembolso', '<=', $obj->fecha_solicitud)
								->estado('DESEMBOLSADO')
								->get();
		foreach ($creditosVigentes as $key => $value) {
			if($value->saldoObligacion($obj->fecha_solicitud) <= 0)$creditosVigentes->forget($key);
		}
		$creditosRecogidos = $obj->obligacionesConsolidadas()->with('creditoConsolidado')->get();
		return view('creditos.solicitudCredito.consolidacion')
				->withSolicitud($obj)
				->withCreditosVigentes($creditosVigentes)
				->withCreditosRecogidos($creditosRecogidos);
	}

	public function putConsolidacion(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		$validador = Validator::make($request->all(), [
			'credito'				=> 'bail|required|exists:sqlsrv.creditos.solicitudes_creditos,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL',
			'tipo_consolidacion'	=> 'bail|required|string|in:SALDOTOTAL,INCLUIDORECAUDO,PARCIAL'
		]);
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO,BORRADOR')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		if($validador->fails()) {
			Session::flash('error', 'No es posible recoger esta obligación');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$credito = SolicitudCredito::find($request->credito);
		if($credito->tercero != $obj->tercero) {
			Session::flash('error', 'La obligación no pertenece al tercero');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		if($credito->estado_solicitud != 'DESEMBOLSADO') {
			Session::flash('error', 'Estado no válido de obligación a recoger');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$obligacionesRecogidas = $obj->obligacionesConsolidadas()->creditoConsolidado($credito->id)->first();
		if(!empty($obligacionesRecogidas)) {
			Session::flash('error', 'La obligación ya ha sido recogida');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$capital = $credito->saldoObligacion('01/01/3000');
		$intereses = $credito->saldoInteresObligacion($obj->fecha_solicitud);
		if($request->tipo_consolidacion == 'INCLUIDORECAUDO') {
			$recaudo = optional($credito->proximoRecaudo())->capital_generado;
			$capital -= empty($recaudo) ? 0 : $recaudo;
		}
		if($capital <= 0) {
			Session::flash('error', 'El crédito no tiene saldo para recoger');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$recogida = new ObligacionConsolidacion;
		$recogida->solicitud_credito_id = $obj->id;
		$recogida->solicitud_credito_consolidado_id = $credito->id;
		$recogida->pago_capital = $capital;
		$recogida->pago_intereses = $intereses;
		$recogida->tipo_consolidacion = $request->tipo_consolidacion;
		$recogida->save();
		Session::flash('message', 'Se ha recogido la obligación');
		return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
	}

	public function deleteConsolidacion(SolicitudCredito $obj, Request $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		$validador = Validator::make($request->all(), [
			'credito'				=> 'bail|required|exists:sqlsrv.creditos.obligaciones_consolidacion,id,deleted_at,NULL',
		]);
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO,BORRADOR')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		if($validador->fails()) {
			Session::flash('error', 'No es posible recoger esta obligación');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$creditoRecogido = ObligacionConsolidacion::find($request->credito);
		if($creditoRecogido->solicitud_credito_id != $obj->id) {
			Session::flash('error', 'La obligación no pertenece al tercero');
			return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
		}
		$creditoRecogido->delete();
		Session::flash('message', 'Obligación desasociada para la consolidación');
		return redirect()->route('solicitudCreditoConsolidacion', $obj->id);
	}

	private function listaProgramaciones($obj) {
		$socio = $obj->tercero->socio;
		$listaProgramaciones = array();
		if($socio) {
			$programaciones = $socio->pagaduria->calendarioRecaudos()->whereEstado('PROGRAMADO')->get();
			foreach($programaciones as $programacion) {
				$listaProgramaciones[$programacion->fecha_recaudo->format('d/m/Y')] = $programacion->fecha_recaudo;
			}
		}
		return $listaProgramaciones;
	}

	public function cuotaExtraordinarias(SolicitudCredito $obj) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		$periodicidades = array(
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
		$listaProgramaciones = $this->listaProgramaciones($obj);
		$cuotas = $obj->cuotasExtraordinarias;
		return view('creditos.solicitudCredito.cuotasExtraordinarias')
				->withSolicitud($obj)
				->withPeriodicidades($periodicidades)
				->withProgramaciones($listaProgramaciones)
				->withCuotas($cuotas);
	}

	public function putCuotaExtraordinarias(SolicitudCredito $obj, MakeCuotaExtraordinariaRequest $request) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		$cuotas = new CuotaExtraordinaria;

		$cuotas->obligacion_id = $obj->id;
		$cuotas->numero_cuotas = $request->numero_cuotas;
		$cuotas->valor_cuota = $request->valor_cuota;
		$cuotas->forma_pago = $request->forma_pago;
		$cuotas->periodicidad = $request->periodicidad;
		$cuotas->inicio_descuento = $request->inicio_descuento;

		$cuotas->save();

		Session::flash("message", "Se ha creado la cuota extraorinaria");
		return redirect()->route('solicitudCredito.cuotasExtraordinarias', $obj->id);
	}

	public function deleteCuotaExtraordinarias(SolicitudCredito $obj, CuotaExtraordinaria $cuota) {
		$this->objEntidad($obj, 'No está autorizado a ingresar a la solicitud de crédito');
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		if($obj->id != $cuota->obligacion_id) {
			Session::flash('error', 'Cuota extraordinaria no pertenece a la solicitud de crédito');
			return redirect('solicitudCredito');
		}
		$cuota->delete();
		Session::flash("message", "Se ha eliminado la cuota extraorinaria");
		return redirect()->route('solicitudCredito.cuotasExtraordinarias', $obj->id);
	}

	public function putConsolidacionModificar(SolicitudCredito $obj, EditConsolidacionSaldoRequest $request) {
		$log = "Ingresó a modificar el saldo del credito '%s' por recogida de créditos";
		$this->log(sprintf($log, $obj->id));
		$this->objEntidad($obj, 'No está autorizado a ingresar a modificar la solicitud de crédito');
		if($obj->tieneEstados('SALDADO,DESEMBOLSADO,ANULADO,RECHAZADO')) {
			Session::flash('error', 'Solicitud con estado no válido para la recogida de créditos');
			return redirect('solicitudCredito');
		}
		$creditosRecogidos = $obj->obligacionesConsolidadas()->with('creditoConsolidado')->get();
		$total = 0;
		foreach ($creditosRecogidos as $credito) $total += $credito->total;
		$total += $request->valorDesembolso;
		$obj->valor_solicitud = $total;
		$obj->valor_credito = $total;
		$obj->save();
		$res = DB::select('exec creditos.sp_amortizacion_credito ?', [$obj->id]);
		if ($res) {
			if ($res[0]->ERROR == 1) {
				Session::flash('error', $res[0]->MENSAJE);
				return redirect()->route('solicitudCreditoConsolidacion', $obj);
			}
		}
		Session::flash("message", "Se ha recalculado el monto total de la solicitud");
		return redirect()->route('solicitudCreditoConsolidacion', $obj);
	}

	public static function routes() {
		Route::get('solicitudCredito', 'Creditos\SolicitudCreditoController@index');
		Route::get('solicitudCredito/create', 'Creditos\SolicitudCreditoController@create');
		Route::post('solicitudCredito', 'Creditos\SolicitudCreditoController@store');
		Route::get('solicitudCredito/{obj}/edit', 'Creditos\SolicitudCreditoController@edit')->name('solicitudCreditoEdit');
		Route::put('solicitudCredito/{obj}', 'Creditos\SolicitudCreditoController@update');
		Route::get('solicitudCredito/calcularAmortizacion', 'Creditos\SolicitudCreditoController@calcularAmortizacion');
		Route::get('solicitudCredito/{obj}/getTasaCondicionada', 'Creditos\SolicitudCreditoController@getTasaCondicionada');
		Route::get('solicitudCredito/{obj}/radicar', 'Creditos\SolicitudCreditoController@radicar')->name('solicitudCreditoRadicar');
		Route::get('solicitudCredito/{obj}/aprobar', 'Creditos\SolicitudCreditoController@aprobar')->name('solicitudCreditoAprobar');
		Route::put('solicitudCredito/{obj}/aprobar', 'Creditos\SolicitudCreditoController@aprobarUpdate');
		Route::get('solicitudCredito/{obj}/alternarCondicion', 'Creditos\SolicitudCreditoController@alternarCondicion')->name('solicitudCreditoAlternarCondicion');
		Route::get('solicitudCredito/{obj}/alternarDocumento', 'Creditos\SolicitudCreditoController@alternarDocumento')->name('solicitudCreditoAlternarDocumento');
		Route::get('solicitudCredito/{obj}/anular', 'Creditos\SolicitudCreditoController@anular')->name('solicitudCreditoAnular');
		Route::put('solicitudCredito/{obj}/anular', 'Creditos\SolicitudCreditoController@anularUpdate');
		Route::get('solicitudCredito/{obj}/rechazar', 'Creditos\SolicitudCreditoController@rechazar')->name('solicitudCreditoRechazar');
		Route::put('solicitudCredito/{obj}/rechazar', 'Creditos\SolicitudCreditoController@rechazarUpdate');
		Route::get('solicitudCredito/{obj}/desembolsar', 'Creditos\SolicitudCreditoController@desembolsar')->name('solicitudCreditoDesembolsar');
		Route::put('solicitudCredito/{obj}/desembolsar', 'Creditos\SolicitudCreditoController@desembolsarUpdate');
		Route::put('solicitudCredito/{obj}/procesar', 'Creditos\SolicitudCreditoController@procesar');

		Route::get('solicitudCredito/{obj}/garantias', 'Creditos\SolicitudCreditoController@garantias')->name('solicitudCreditoGarantias');
		Route::post('solicitudCredito/{obj}/codeudor', 'Creditos\SolicitudCreditoController@postCodeudor')->name('solicitudCreditoPostCodeudor');
		Route::get('solicitudCredito/{obj}/codeudor/{codeudor}', 'Creditos\SolicitudCreditoController@deleteCodeudor')->name('solicitudCreditoDeleteCodeudor');

		Route::get('solicitudCredito/{obj}/consolidacion', 'Creditos\SolicitudCreditoController@consolidacion')->name('solicitudCreditoConsolidacion');
		Route::put('solicitudCredito/{obj}/consolidacion', 'Creditos\SolicitudCreditoController@putConsolidacion')->name('solicitudCreditoPutConsolidacion');
		Route::put('solicitudCredito/{obj}/consolidacionModificar', 'Creditos\SolicitudCreditoController@putConsolidacionModificar')->name('solicitudCredito.put.consolidacion.modificar');
		Route::delete('solicitudCredito/{obj}/consolidacion', 'Creditos\SolicitudCreditoController@deleteConsolidacion')->name('solicitudCreditoDeleteConsolidacion');

		Route::get('solicitudCredito/{obj}/cuotasExtraordinarias', 'Creditos\SolicitudCreditoController@cuotaExtraordinarias')->name('solicitudCredito.cuotasExtraordinarias');
		Route::put('solicitudCredito/{obj}/cuotasExtraordinarias', 'Creditos\SolicitudCreditoController@putCuotaExtraordinarias')->name('solicitudCredito.put.cuotasExtraordinarias');
		Route::get('solicitudCredito/{obj}/cuotasExtraordinarias/{cuota}', 'Creditos\SolicitudCreditoController@deleteCuotaExtraordinarias')->name('solicitudCredito.delete.cuotasExtraordinarias');
	}
}
