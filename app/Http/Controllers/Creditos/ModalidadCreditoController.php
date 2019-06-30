<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\Modalidad\CreateModalidadRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadAmortizacionRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadCondicionesRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadCupoRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadDocumentacionRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadDocumentosDocumentacionRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadPlazoRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadRangoCupoRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadRangoTasaRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadTarjetaRequest;
use App\Http\Requests\Creditos\Modalidad\EditModalidadTasaRequest;
use App\Models\Creditos\CondicionModalidad;
use App\Models\Creditos\DocumentacionModalidad;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\RangoCondicionModalidad;
use App\Models\Creditos\TipoGarantia;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class ModalidadCreditoController extends Controller
{
	use FonadminTrait;
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$modalidadesCreditos = Modalidad::entidadId()->search($request->name)->paginate();
		return view('creditos.modalidad.index')->withModalidades($modalidadesCreditos);
	}

	public function create() {		
		return view('creditos.modalidad.create');
	}

	public function store(CreateModalidadRequest $request) {
		$modalidad = $this->getEntidad()->modalidadesCreditos()->create($request->all());
		Session::flash('message', 'Se ha creado la modalidad de crédito \'' . $modalidad->codigo . ' - ' . $modalidad->nombre . '\'');
		return redirect()->route('modalidadCreditoEdit', $modalidad);
	}

	public function edit(Modalidad $obj) {
		$this->objEntidad($obj);
		return view('creditos.modalidad.edit')->withModalidad($obj);
	}

	public function update(Modalidad $obj, EditModalidadRequest $request) {
		$this->objEntidad($obj);
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;
		if($request->plazo_condicionado) {
			$obj->es_plazo_condicionado = true;
			$obj->plazo = null;
			$obj->save();

			$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'PLAZO')->first();
			if($condicion == null) {
				$condicion = new CondicionModalidad;
				
				$condicion->tipo_condicion = 'PLAZO';
				$condicion->condicionado_por = $request->condicionPor;
				$obj->condicionesModalidad()->save($condicion);
			}
			else {
				$condicion->tipo_condicion = 'PLAZO';
				$condicion->condicionado_por = $request->condicionPor;
				$condicion->save();
			}		

			Session::flash('message', 'Se ha actualizado la categoría plazo, proceda a agregar los rangos de la condición');
			return redirect()->route('modalidadCreditoEditTasa', $obj);
		}
		else {
			$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'PLAZO')->first();
			if($condicion != null) {
				$condicion->delete();
			}

			$obj->es_plazo_condicionado = false;
			$obj->plazo = $request->plazo;
			$obj->save();

			Session::flash('message', 'Se ha actualizado la categoría plazo');
			return redirect()->route('modalidadCreditoEditTasa', $obj);
		}
	}

	public function updatePlazo(Modalidad $obj, EditModalidadPlazoRequest $request) {
		$this->objEntidad($obj);
		$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'PLAZO')->first();
		if($condicion == null)return;

		$rango = new RangoCondicionModalidad;
		$rango->condicionado_desde = $request->condicionadoDesde;
		$rango->condicionado_hasta = $request->condicionadoHasta;
		$rango->tipo_condicion_minimo = $request->tipoCondicionMinimo;
		$rango->tipo_condicion_maximo = $request->tipoCondicionMaximo;

		$condicion->rangosCondicionesModalidad()->save($rango);

		$data = [
			'id'						=> $rango->id,
			'condicion_modalidad'		=> $rango->condicion_modalidad,
			'condicionado_desde'		=> number_format($rango->condicionado_desde, 0),
			'condicionado_hasta'		=> number_format($rango->condicionado_hasta, 0),
			'tipo_condicion_minimo'		=> number_format($rango->tipo_condicion_minimo, 0),
			'tipo_condicion_maximo'		=> number_format($rango->tipo_condicion_maximo, 0)
		];
		return response()->json($data);
	}

	public function deletePlazo(RangoCondicionModalidad $obj) {
		$obj->delete();
		return response()->json(['ok' => true]);
	}

	public function editTasa(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		return view('creditos.modalidad.editTasa')->withModalidad($obj);
	}

	public function tasa(Modalidad $obj, EditModalidadTasaRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;

		$obj->tipo_tasa = $request->tipo_tasa;

		switch($request->tipo_tasa) {
			case 'FIJA':
				$obj->pago_interes = $request->pago_interes;
				$obj->aplica_mora = $request->aplica_mora;
				$obj->tasa_mora = $request->aplica_mora == '1' ? $request->tasa_mora : null;
				if($request->tasa_condicionada) {
					$obj->es_tasa_condicionada = true;
					$obj->tasa = null;
					$obj->save();

					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion == null) {
						$condicion = new CondicionModalidad;						
						$condicion->tipo_condicion = 'TASA';
						$condicion->condicionado_por = $request->condicionPor;
						$obj->condicionesModalidad()->save($condicion);
					}
					else {
						$condicion->tipo_condicion = 'TASA';
						$condicion->condicionado_por = $request->condicionPor;
						$condicion->save();
					}
					Session::flash('message', 'Se ha actualizado la categoría tasa, proceda a agregar los rangos de la condición');
					return redirect()->route('modalidadCreditoEditTasa', $obj);
				}
				else {
					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion != null) {
						$condicion->delete();
					}
					$obj->es_tasa_condicionada = false;
					$obj->tasa = $request->tasa;
					$obj->save();

					Session::flash('message', 'Se ha actualizado la categoría tasa');
					return redirect()->route('modalidadCreditoEditCupo', $obj);
				}
				break;
			case 'VARIABLE':
				$obj->pago_interes = $request->pago_interes;
				$obj->aplica_mora = $request->aplica_mora;
				$obj->tasa_mora = $request->aplica_mora == '1' ? $request->tasa_mora : null;
				if($request->tasa_condicionada) {
					$obj->es_tasa_condicionada = true;
					$obj->tasa = null;
					$obj->save();

					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion == null) {
						$condicion = new CondicionModalidad;						
						$condicion->tipo_condicion = 'TASA';
						$condicion->condicionado_por = $request->condicionPor;
						$obj->condicionesModalidad()->save($condicion);
					}
					else {
						$condicion->tipo_condicion = 'TASA';
						$condicion->condicionado_por = $request->condicionPor;
						$condicion->save();
					}
					Session::flash('message', 'Se ha actualizado la categoría tasa, proceda a agregar los rangos de la condición');
					return redirect()->route('modalidadCreditoEditTasa', $obj);
				}
				else {
					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion != null) {
						$condicion->delete();
					}
					$obj->es_tasa_condicionada = false;
					$obj->tasa = $request->tasa;
					$obj->save();

					Session::flash('message', 'Se ha actualizado la categoría tasa');
					return redirect()->route('modalidadCreditoEditCupo', $obj);
				}
				break;
			case 'SINTASA':
				$obj->tipo_cuota = 'CAPITAL';
				$obj->pago_interes = null;
				$obj->aplica_mora = $request->aplica_mora;
				$obj->tasa_mora = $request->aplica_mora == '1' ? $request->tasa_mora : null;
				$obj->es_tasa_condicionada = 0;
				$obj->tasa = null;
				$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
				if($condicion != null) {
					$condicion->delete();
				}
				$obj->save();
				Session::flash('message', 'Se ha actualizado la categoría tasa');
				return redirect()->route('modalidadCreditoEditCupo', $obj);
				break;
		}
	}

	public function updateTasa(Modalidad $obj, EditModalidadRangoTasaRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();

		if($condicion == null)return;

		$rango = new RangoCondicionModalidad;
		$rango->condicionado_desde = $request->condicionadoDesde;
		$rango->condicionado_hasta = $request->condicionadoHasta;
		$rango->tipo_condicion_minimo = $request->tipoCondicionTasa;
		$rango->tipo_condicion_maximo = $request->tipoCondicionTasa;

		$condicion->rangosCondicionesModalidad()->save($rango);

		$data = [
			'id'						=> $rango->id,
			'condicion_modalidad'		=> $rango->condicion_modalidad,
			'condicionado_desde'		=> number_format($rango->condicionado_desde, 0),
			'condicionado_hasta'		=> number_format($rango->condicionado_hasta, 0),
			'tipo_condicion_tasa'		=> number_format($rango->tipo_condicion_minimo, 2),
		];

		return response()->json($data);
	}

	public function deleteTasa(RangoCondicionModalidad $obj) {
		$obj->delete();
		return response()->json(['ok' => true]);
	}

	public function editCupo(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->monto = substr($obj->monto, 0, strpos($obj->monto, "."));
		return view('creditos.modalidad.editCupo')->withModalidad($obj);
	}

	public function cupo(Modalidad $obj, EditModalidadCupoRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;

		$obj->afecta_cupo = $request->afecta_cupo;
		if($request->es_monto_condicionado) {
			$obj->es_monto_condicionado = true;
			$obj->monto = null;
			$obj->save();

			$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();
			if($condicion == null) {
				$condicion = new CondicionModalidad;				
				$condicion->tipo_condicion = 'MONTO';
				$condicion->condicionado_por = $request->condicionPor;
				$obj->condicionesModalidad()->save($condicion);
			}
			else {
				$condicion->tipo_condicion = 'MONTO';
				$condicion->condicionado_por = $request->condicionPor;
				$condicion->save();
			}
			Session::flash('message', 'Se ha actualizado la categoría cupo, proceda a agregar los rangos de la condición');
			return redirect()->route('modalidadCreditoEditCupo', $obj);
		}
		else {
			$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();
			if($condicion != null) {
				$condicion->delete();
			}

			$obj->es_monto_condicionado = false;
			if($request->es_monto_cupo == '1') {
				$obj->es_monto_cupo = true;
				$obj->monto = null;
			}
			else {
				$obj->es_monto_cupo = false;
				$obj->monto = $request->monto;
			}
			$obj->save();

			Session::flash('message', 'Se ha actualizado la categoría cupo');
			return redirect()->route('modalidadCreditoEditCupo', $obj);
		}
	}

	public function updateCupo(Modalidad $obj, EditModalidadRangoCupoRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();

		if($condicion == null)return;

		$rango = new RangoCondicionModalidad;
		$rango->condicionado_desde = $request->condicionadoDesde;
		$rango->condicionado_hasta = $request->condicionadoHasta;
		$rango->tipo_condicion_minimo = $request->tipoCondicionMinimo;
		$rango->tipo_condicion_maximo = $request->tipoCondicionMaximo;

		$condicion->rangosCondicionesModalidad()->save($rango);

		$data = [
			'id'						=> $rango->id,
			'condicion_modalidad'		=> $rango->condicion_modalidad,
			'condicionado_desde'		=> number_format($rango->condicionado_desde, 0),
			'condicionado_hasta'		=> number_format($rango->condicionado_hasta, 0),
			'tipo_condicion_minimo'		=> number_format($rango->tipo_condicion_minimo, 1),
			'tipo_condicion_maximo'		=> number_format($rango->tipo_condicion_maximo, 1),
		];

		return response()->json($data);
	}

	public function deleteCupo(RangoCondicionModalidad $obj) {
		$obj->delete();
		return response()->json(['ok' => true]);
	}

	public function editAmortizacion(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$hayTasa = false;
		if(!empty($obj->tipo_tasa)) {
			switch($obj->tipo_tasa) {
				case 'FIJA':
					if(empty($obj->pago_interes)) {
						break;
					}
					if(!empty($obj->tasa)) {
						$hayTasa = true;
						break;
					}
					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion == null) {
						break;
					}
					if($condicion->rangosCondicionesModalidad->count()) {
						$hayTasa = true;
						break;
					}
					break;
				case 'VARIABLE':
					if(empty($obj->pago_intereses)) {
						break;
					}
					if(!empty($obj->factor_condicion_variable_id)) {
						$hayTasa = true;
						break;
					}
					if(!empty($obj->tasa)) {
						$hayTasa = true;
						break;
					}
					$condicion = $obj->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
					if($condicion == null) {
						break;
					}
					if($condicion->rangosCondicionesModalidad->count()) {
						$hayTasa = true;
						break;
					}
					break;

				case 'SINTASA':
					$hayTasa = true;
					break;
				
				default:
					$hayTasa = false;
					break;
			}
		}

		if($hayTasa) {
			return view('creditos.modalidad.editAmortizacion')->withModalidad($obj);
		}
		else {
			Session::flash('error', 'Para configurar amortización, se requiere la parametrización de Tasa');
			return redirect()->route('modalidadCreditoEditTasa', $obj);
		}
	}

	public function amortizacion(Modalidad $obj, EditModalidadAmortizacionRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;
		$obj->tipo_cuota = $request->tipo_cuota;

		$obj->acepta_pago_semanal = 0;
		$obj->acepta_pago_decadal = 0;
		$obj->acepta_pago_catorcenal = 0;
		$obj->acepta_pago_quincenal = 0;
		$obj->acepta_pago_mensual = 0;
		$obj->acepta_pago_bimestral = 0;
		$obj->acepta_pago_trimestral = 0;
		$obj->acepta_pago_cuatrimestral = 0;
		$obj->acepta_pago_semestral = 0;
		$obj->acepta_pago_anual = 0;

		foreach($request->periodicidades_admitidas as $periodicidad) {
			if($periodicidad == 'semanal')$obj->acepta_pago_semanal = 1;
			if($periodicidad == 'decadal')$obj->acepta_pago_decadal = 1;
			if($periodicidad == 'catorcenal')$obj->acepta_pago_catorcenal = 1;
			if($periodicidad == 'quincenal')$obj->acepta_pago_quincenal = 1;
			if($periodicidad == 'mensual')$obj->acepta_pago_mensual = 1;
			if($periodicidad == 'bimestral')$obj->acepta_pago_bimestral = 1;
			if($periodicidad == 'trimestral')$obj->acepta_pago_trimestral = 1;
			if($periodicidad == 'cuatrimestral')$obj->acepta_pago_cuatrimestral = 1;
			if($periodicidad == 'semestral')$obj->acepta_pago_semestral = 1;
			if($periodicidad == 'anual')$obj->acepta_pago_anual = 1;
		}

		$obj->acepta_cuotas_extraordinarias = $request->acepta_cuotas_extraordinarias;
		if($request->acepta_cuotas_extraordinarias) {
			$obj->maximo_porcentaje_pago_extraordinario = $request->maximo_porcentaje_pago_extraordinario;
		}
		else {
			$obj->maximo_porcentaje_pago_extraordinario = null;
		}

		$obj->save();

		Session::flash('message', 'Se ha actualizado la categoría amortización');
		return redirect()->route('modalidadCreditoEditCondiciones', $obj);
	}

	public function editCondiciones(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		return view('creditos.modalidad.editCondiciones')->withModalidad($obj);
	}

	public function condiciones(Modalidad $obj, EditModalidadCondicionesRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;

		$obj->minimo_antiguedad_entidad = $request->requiereAntiguedadEntidad == '1' ? $request->minimo_antiguedad_entidad : null;
		$obj->minimo_antiguedad_empresa = $request->requiereAntiguedadLaboral == '1' ? $request->minimo_antiguedad_empresa : null;
		$obj->limite_obligaciones = $request->limiteObligacionesModalidad == '1' ? $request->limite_obligaciones : null;
		$obj->intervalo_solicitudes = $request->intervaloSolcitudes == '1' ? $request->intervalo_solicitudes : null;

		$obj->save();

		Session::flash('message', 'Se ha actualizado la categoría condiciones');
		return redirect()->route('modalidadCreditoEditDocumentacion', $obj);
	}

	public function editDocumentacion(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		return view('creditos.modalidad.editDocumentacion')->withModalidad($obj);
	}

	public function documentacion(Modalidad $obj, EditModalidadDocumentacionRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->nombre = $request->nombre;
		$obj->descripcion = $request->descripcion;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;

		$obj->save();

		Session::flash('message', 'Se ha actualizado la modalidad \'' . $obj->codigo . ' - ' . $obj->nombre . '\'');
		return redirect('modalidadCredito');
	}

	public function updateDocumentacion(Modalidad $obj, EditModalidadDocumentosDocumentacionRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$documento = new DocumentacionModalidad;
		$documento->documento = $request->documento;
		$documento->obligatorio = $request->obligatorio;

		$obj->documentacionModalidad()->save($documento);

		$data = [
			'id'						=> $documento->id,
			'documento'					=> $documento->documento,
			'obligatorio'				=> $documento->obligatorio ? 'Obligatorio' : 'Opcional',
		];

		return response()->json($data);
	}

	public function deleteDocumentacion(DocumentacionModalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$obj->delete();
		return response()->json(['ok' => true]);
	}

	public function editGarantias(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$tipoGarantias = TipoGarantia::entidadId()->activa()->orderBy('nombre')->pluck('nombre', 'id');
		return view('creditos.modalidad.garantias')->withModalidad($obj)->withTiposGarantias($tipoGarantias);
	}

	public function updateGarantias(Modalidad $obj, Request $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$request->validate([
			'garantia'	=> [
							'bail',
							'exists:sqlsrv.creditos.tipos_garantia,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL'
						]
		]);

		$tipoGarantia = TipoGarantia::find($request->garantia);
		$obj->tiposGarantias()->attach($tipoGarantia);
		$data = array(
			'id'			=> $tipoGarantia->id,
			'nombre'		=> $tipoGarantia->nombre,
			'tipoGarantia'	=> $tipoGarantia->tipo_garantia
		);
		return response()->json($data);
	}

	public function deleteGarantias(Modalidad $obj, TipoGarantia $garantia) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$id = DB::table('creditos.garantia_modalidad')->whereModalidadCreditoId($obj->id)
				->select('id')
				->whereTipoGarantiaId($garantia->id)
				->take(1)
				->get();
		DB::table('creditos.garantia_modalidad')->whereModalidadCreditoId($obj->id)
				->whereId($id[0]->id)
				->delete();
		return response()->json(['ok' => true]);
	}

	public function editTarjeta(Modalidad $obj) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$this->log("Ingresó a editar información de tarjeta para la modalidad " . $obj->id);
		$cantidadSolicitudes = $obj->solicitudesCreditos()->count();
		return view('creditos.modalidad.editTarjeta')->withModalidad($obj)->withCantidadSolicitudes($cantidadSolicitudes);
	}

	public function updateTarjeta(Modalidad $obj, EditModalidadTarjetaRequest $request) {
		$this->objEntidad($obj, 'No tiene permiso para acceder a esta modalidad de crédito');
		$this->log("Actualizó la modalidad con los siguientes parámetros " . json_encode($request->all()), "ACTUALIZAR");
		$obj->nombre = $request->nombre;
		$obj->es_exclusivo_de_socios = $request->es_exclusivo_de_socios;
		$obj->esta_activa = $request->esta_activa;
		$obj->descripcion = $request->descripcion;
		if(!$obj->solicitudesCreditos()->count()) {
			$obj->uso_para_tarjeta = $request->uso_para_tarjeta;
			Session::flash('message', 'Se ha actualizado la categoría Tarjeta');
		}
		else {
			Session::flash('message', 'Se ha actualizado la modalidad');
		}
		$obj->save();
		return redirect()->route('modalidadCreditoEditTarjeta', $obj);
	}

	public static function routes() {
		Route::get('modalidadCredito', 'Creditos\ModalidadCreditoController@index');
		Route::get('modalidadCredito/create', 'Creditos\ModalidadCreditoController@create');
		Route::post('modalidadCredito', 'Creditos\ModalidadCreditoController@store');


		Route::get('modalidadCredito/{obj}/edit', 'Creditos\ModalidadCreditoController@edit')->name('modalidadCreditoEdit');
		Route::put('modalidadCredito/{obj}', 'Creditos\ModalidadCreditoController@update');
		Route::put('modalidadCredito/{obj}/plazo', 'Creditos\ModalidadCreditoController@updatePlazo');
		Route::delete('modalidadCredito/{obj}/plazo', 'Creditos\ModalidadCreditoController@deletePlazo');

		Route::get('modalidadCredito/{obj}/editTasa', 'Creditos\ModalidadCreditoController@editTasa')->name('modalidadCreditoEditTasa');
		Route::put('modalidadCredito/{obj}/tasa', 'Creditos\ModalidadCreditoController@tasa');
		Route::put('modalidadCredito/{obj}/updateTasa', 'Creditos\ModalidadCreditoController@updateTasa');
		Route::delete('modalidadCredito/{obj}/tasa', 'Creditos\ModalidadCreditoController@deleteTasa');

		Route::get('modalidadCredito/{obj}/editCupo', 'Creditos\ModalidadCreditoController@editCupo')->name('modalidadCreditoEditCupo');
		Route::put('modalidadCredito/{obj}/cupo', 'Creditos\ModalidadCreditoController@cupo');
		Route::put('modalidadCredito/{obj}/updateCupo', 'Creditos\ModalidadCreditoController@updateCupo');
		Route::delete('modalidadCredito/{obj}/cupo', 'Creditos\ModalidadCreditoController@deleteCupo');

		Route::get('modalidadCredito/{obj}/editAmortizacion', 'Creditos\ModalidadCreditoController@editAmortizacion')->name('modalidadCreditoEditAmortizacion');
		Route::put('modalidadCredito/{obj}/amortizacion', 'Creditos\ModalidadCreditoController@amortizacion');

		Route::get('modalidadCredito/{obj}/editCondiciones', 'Creditos\ModalidadCreditoController@editCondiciones')->name('modalidadCreditoEditCondiciones');
		Route::put('modalidadCredito/{obj}/condiciones', 'Creditos\ModalidadCreditoController@condiciones');


		Route::get('modalidadCredito/{obj}/editDocumentacion', 'Creditos\ModalidadCreditoController@editDocumentacion')->name('modalidadCreditoEditDocumentacion');
		Route::put('modalidadCredito/{obj}/documentacion', 'Creditos\ModalidadCreditoController@documentacion');
		Route::put('modalidadCredito/{obj}/updateDocumentacion', 'Creditos\ModalidadCreditoController@updateDocumentacion');
		Route::delete('modalidadCredito/{obj}/documentacion', 'Creditos\ModalidadCreditoController@deleteDocumentacion');

		Route::get('modalidadCredito/{obj}/editGarantias', 'Creditos\ModalidadCreditoController@editGarantias')->name('modalidadCreditoEditGarantias');
		Route::put('modalidadCredito/{obj}/updateGarantias', 'Creditos\ModalidadCreditoController@updateGarantias')->name('modalidadCreditoUpdateGarantias');
		Route::delete('modalidadCredito/{obj}/{garantia}', 'Creditos\ModalidadCreditoController@deleteGarantias');

		Route::get('modalidadCredito/{obj}/editTarjeta', 'Creditos\ModalidadCreditoController@editTarjeta')->name('modalidadCreditoEditTarjeta');
		Route::put('modalidadCredito/{obj}/editTarjeta', 'Creditos\ModalidadCreditoController@updateTarjeta')->name('modalidadCreditoUpdateTarjeta');
	}
}
