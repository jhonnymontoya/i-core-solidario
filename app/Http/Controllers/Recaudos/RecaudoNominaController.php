<?php

namespace App\Http\Controllers\Recaudos;

use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Recaudos\RecaudoNomina\CargarPlanoRequest;
use App\Events\Tarjeta\CalcularAjusteAhorrosVista;
use App\Models\General\Tercero;
use App\Models\Recaudos\ControlProceso;
use App\Models\Recaudos\DatoParaAplicar;
use App\Models\Recaudos\Pagaduria;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Storage;
use Validator;

class RecaudoNominaController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		Validator::make($request->all(), [
			'pagaduria'			=> [
									'bail',
									'exists:sqlsrv.recaudos.pagadurias,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL'
								],
		])->validate();
		$pagadurias = Pagaduria::entidadId()->orderBy('nombre')->pluck('nombre', 'id');
		$pagaduria = null;
		if(!empty($request->pagaduria)) {
			$pagaduria = Pagaduria::find($request->pagaduria);
		}
		return view('recaudos.recaudosNomina.index')->withPagadurias($pagadurias)->withPagaduria($pagaduria);
	}

	public function procesar(Pagaduria $obj, Request $request) {
		$periodo = $obj->calendarioRecaudos()->whereEstado('PROGRAMADO')->orderBy('fecha_recaudo')->first();
		if(empty($periodo)) {
			Session::flash('error', 'Periodo a procesar no se encuentra programado');
			return redirect('recaudosNomina?pagaduria=' . $obj->id);
		}
		if($this->moduloCerrado(7, $periodo->fecha_recaudo)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->back();
		}
		$res = DB::select('exec recaudos.sp_generacion_recaudos_nomina ?, ?', [$obj->id, $periodo->fecha_recaudo]);
		if($res[0]->ERROR) {
			Session::flash('error', $res[0]->MENSAJE);
			return redirect('recaudosNomina?pagaduria=' . $obj->id);
		}
		Session::flash('message', $res[0]->MENSAJE);
		return redirect('recaudosNomina?pagaduria=' . $obj->id);
	}

	public function gestionRecaudoNomina(ControlProceso $obj) {
		$recaudosNomina = $obj->recaudosNomina()->select(
								'concepto_recaudo_id',
								DB::raw('SUM(capital_generado) + SUM(intereses_generado) + SUM(seguro_generado) as generado'),
								DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) + SUM(seguro_aplicado) as aplicado'),
								DB::raw('SUM(capital_ajustado) + SUM(intereses_ajustado) + SUM(seguro_ajustado) as ajustado')
							)
							->groupBy('concepto_recaudo_id')
							->get();
		return view('recaudos.recaudosNomina.gestion')->withControlProceso($obj)->withRecaudosNomina($recaudosNomina);
	}

	public function aplicarNomina(ControlProceso $obj) {
		if($obj->estado != 'GENERADO') {
			Session::flash('error', 'Estado invalido del proceso para aplicación');
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}
		$recaudosNomina = $obj->recaudosNomina()->select(
													'concepto_recaudo_id',
													DB::raw('SUM(capital_generado) + SUM(intereses_generado) as generado'),
													DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) as aplicado'),
													DB::raw('SUM(capital_ajustado) + SUM(intereses_ajustado) as ajustado')
							)
							->groupBy('concepto_recaudo_id')
							->get();
		return view('recaudos.recaudosNomina.aplicarNomina')->withControlProceso($obj)->withRecaudosNomina($recaudosNomina);
	}

	public function generarDatosAplicar(ControlProceso $obj) {
		if($obj->estado != 'GENERADO') {
			Session::flash('error', 'Estado invalido del proceso para generar');
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}
		$res = DB::select('EXEC recaudos.sp_carga_datos_generacion_para_aplicar ?', [$obj->id]);
		if(empty($res)) {
			Session::flash('error', 'Estado al procesar la generación');
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		$res = $res[0];
		if($res->ERROR) {
			Session::flash('error', $res->MENSAJE);
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		else {
			Session::flash('message', $res->MENSAJE);
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
	}

	public function eliminarDatosAplicar(ControlProceso $obj) {
		if($obj->estado != 'GENERADO') {
			Session::flash('error', 'Estado invalido del proceso para eliminar');
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}
		DatoParaAplicar::where('control_proceso_id', $obj->id)->delete();
		Session::flash('message', 'Se ha eliminado con exito los datos para aplicar');
		return redirect()->route('recaudosNominaAplicar', $obj->id);
	}

	public function procesarRecaudos(ControlProceso $obj) {
		if($obj->estado != 'GENERADO') {
			Session::flash('error', 'Estado invalido del proceso para aplicar recaudos');
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}
		if($this->moduloCerrado(7, $obj->calendarioRecaudo->fecha_recaudo)) {
			Session::flash('error', 'Error: Módulo de cartera cerrado para la fecha de proceso');
			return redirect()->back();
		}
		$res = DB::select('EXEC recaudos.sp_aplicacion_recaudos_nomina ?', [$obj->id]);
		if(empty($res)) {
			Session::flash('error', 'Error al procesar la aplicación de recaudos');
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		$res = $res[0];
		if($res->ERROR) {
			Session::flash('error', $res->MENSAJE);
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		else {
			if($this->getEntidad()->usa_tarjeta) {
				$soliciudes = $obj->recaudosNomina()->with('solcitudCredito')->whereHas('solcitudCredito')->get();
				$soliciudes->each(function($item, $key){
					if ($item->solcitudCredito->solicitudDeTarjetaHabiente()) {
						$creditoId = $item->solcitudCredito->id;
						event(new SolicitudCreditoAjusteCreado($creditoId));
					}
				});
				$socios = $obj->recaudosNomina()->with('tercero')->get();
				$socios->each(function($item, $key){
					$socio = optional(optional($item)->tercero)->socio;
					if(!is_null($socio)) {
						event(new CalcularAjusteAhorrosVista($socio->id, false));
					}
				});
			}
			Session::flash('message', $res->MENSAJE);
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}
	}

	public function cargarRecaudos(ControlProceso $obj, CargarPlanoRequest $request) {
		$this->objEntidad($obj->pagaduria);
		if($obj->estado != 'GENERADO') {
			Session::flash('error', 'Estado invalido del proceso para cargar');
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		if(DatoParaAplicar::where('control_proceso_id', $obj->id)->count() > 0) {
			Session::flash('error', 'YA EXISTEN DATOS PARA APLICAR EN EL CONTROL PROCESO');
			return redirect()->route('recaudosNominaAplicar', $obj->id);
		}
		$rutaArchivoRecaudos = $request->file('archivoRecaudo')->store('');
		if(!Storage::exists($rutaArchivoRecaudos))abort(500);
		$recaudos = Storage::get($rutaArchivoRecaudos);
		$recaudos = explode("\n", $recaudos);
		if(count($recaudos) <= 1) {
			Session::flash('error', 'Error: archivo de recaudos se encuentra vacio');
			return redirect()->route('recaudosNominaAplicar', ['obj' => $obj->id]);
		}
		$datosRecaudos = collect();
		$errores = collect();
		$fila = 0;
		$cantidadErrores = 0;
		$cantidadCorrectos = 0;

		foreach($recaudos as &$recaudo) {
			if($fila == 0) {
				$fila++;
				continue;
			}
			$recaudo = str_ireplace("\r", "", $recaudo);
			$recaudo = explode(";", $recaudo);
			if(count($recaudo) != 3) {
				$errores->push('El registro ' . $fila . ' no cumple con la estructura.');
				$cantidadErrores++;
			}
			else {
				$recaudo = collect(['tercero_id' => $recaudo[0], 'nombre' => $recaudo[1], 'valor_descontado' => $recaudo[2]]);
				$resultadoValidacion = $this->validar($recaudo);
				if($resultadoValidacion != null) {
					$errores->push('Registro ' . $fila . ': ' . $resultadoValidacion);
					$fila++;
					continue;
				}
				$datoParaAplicar = $this->construirDatoParaAplicar($obj, $recaudo);
				if($datoParaAplicar instanceof DatoParaAplicar) {
					$datosRecaudos->push($datoParaAplicar);
					$cantidadCorrectos++;
				}
				else {
					$cantidadErrores++;
					$errores->push('Registro ' . $fila . ': ' . $datoParaAplicar);
				}
			}
			$fila++;
		}
		foreach ($datosRecaudos as $recaudo)$recaudo->save();
		Storage::delete($rutaArchivoRecaudos);
		if($cantidadCorrectos > 0) {
			Session::flash('message', 'Se han cargado ' . $cantidadCorrectos . ' registros correctos');
		}
		if($cantidadErrores > 0) {
			Session::flash('error', 'Se han encontrado ' . $cantidadErrores . ' errores');
			Session::flash('errores', $errores);
		}
		return redirect()->route('recaudosNominaAplicar', $obj->id);
	}

	private function construirDatoParaAplicar($proceso, $recaudo) {
		$error = null;
		$tercero = Tercero::entidadTercero()->whereNumeroIdentificacion($recaudo['tercero_id'])->first();
		if($tercero == null) {
			$error = 'No se encontró tercero con el número de identificación ' . $recaudo['tercero_id'];
			return $error;
		}
		if(!$tercero->socio) {
			$error = 'No se encontró socio con número de identificación ' . $recaudo['tercero_id'];
			return $error;
		}
		$datoParaAplicar = new DatoParaAplicar;
		$datoParaAplicar->control_proceso_id = $proceso->id;
		$datoParaAplicar->tercero_id = $tercero->id;
		$datoParaAplicar->valor_descontado = $recaudo['valor_descontado'];
		return $datoParaAplicar;
	}

	private function validar($credito) {
		$errores = null;
		$validador = Validator::make($credito->all(), [
				'tercero_id'		=> [
										'bail',
										'required',
										'integer',
										'min:1',
										'exists:sqlsrv.general.terceros,numero_identificacion,entidad_id,' . $this->getEntidad()->id . ',esta_activo,1,deleted_at,NULL',
									],
				'valor_descontado'	=> 'bail|required|integer|min:1'
			],
			[
				'tercero_id.required'		=> 'La :attribute no encontrado',
				'tercero_id.integer'		=> 'La :attribute debe contener un número de identificación válido',
				'tercero_id.min'			=> 'La :attribute debe contener un número de identificación válido',
				'tercero_id.exists'			=> 'La :attribute no corresponde a un tercero',
				'valor_descontado.integer'	=> 'El :attribute debe contener un valor válido',
				'valor_descontado.min'		=> 'El :attribute debe contener un valor válido',
			],
			[
				'tercero_id'		=> 'identificación',
				'valor_descontado'	=> 'valor',
		]);
		if($validador->fails()) {
			$errores = implode(', ', $validador->errors()->all());
		}
		return $errores;
	}

	public function eliminarControlProceso(ControlProceso $obj) {
		if($obj->estado != "GENERADO") {
			Session::flash("error", "El estado del proceso no es válido.");
			return redirect()->route('recaudosNominaGestion', $obj->id);
		}

		$mensaje = "Anuló la generación de nómina del control proceso '%s'";
		$mensaje = sprintf($mensaje, $obj->id);
		$this->log($mensaje, "ELIMINAR");

		$pagaduria = $obj->pagaduria_id;

		$anulacion = DB::select("exec recaudos.sp_anular_generacion ?", [$obj->id]);

		if(!empty($anulacion[0]->ERROR)) {
			Session::flash('error', $anulacion[0]->MENSAJE);
		}
		else {
			Session::flash('message', $anulacion[0]->MENSAJE);
		}
		$url = "%s?pagaduria=%s";
		$url = sprintf($url, url('recaudosNomina'), $pagaduria);
		return redirect($url);
	}

	public static function routes() {
		Route::get('recaudosNomina', 'Recaudos\RecaudoNominaController@index');
		Route::put('recaudosNomina/{obj}/procesar', 'Recaudos\RecaudoNominaController@procesar')->name('recaudosNominaProcesar');
		Route::get('recaudosNomina/{obj}', 'Recaudos\RecaudoNominaController@gestionRecaudoNomina')->name('recaudosNominaGestion');
		Route::get('recaudosNomina/{obj}/aplicar', 'Recaudos\RecaudoNominaController@aplicarNomina')->name('recaudosNominaAplicar');
		Route::get('recaudosNomina/{obj}/generarDatosAplicar', 'Recaudos\RecaudoNominaController@generarDatosAplicar')->name('recaudosNominaGenerarDatosAplicar');
		Route::get('recaudosNomina/{obj}/eliminarDatosAplicar', 'Recaudos\RecaudoNominaController@eliminarDatosAplicar')->name('recaudosNominaEliminarDatosAplicar');
		Route::put('recaudosNomina/{obj}/procesarRecaudos', 'Recaudos\RecaudoNominaController@procesarRecaudos')->name('recaudosNominaProcesarRecaudos');
		Route::put('recaudosNomina/{obj}/cargarRecaudos', 'Recaudos\RecaudoNominaController@cargarRecaudos')->name('recaudosNominaCargarRecaudos');
		Route::delete('recaudosNomina/{obj}', 'Recaudos\RecaudoNominaController@eliminarControlProceso')->name('recaudosNomina.eliminarProceso');
	}
}
