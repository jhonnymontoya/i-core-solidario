<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\ParametrosCalificacionCartera\CreateOrUpdateParametroCalificacionCarteraRequest;
use App\Models\Creditos\ParametroCalificacionCartera;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Route;

class ParametrosCalificacionCarteraController extends Controller
{
	use FonadminTrait;

	/**
	 * Constructor y verifica que el usuario se encuentre logueado,
	 * que se tenga seleccionada una entidad y que el usuario tenga
	 * permiso para ingresar al menú
	 * @return type
	 */
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	/**
	 * Ingreso al menú de calificación de cartera
	 * @return type
	 */
	public function index() {
		$this->log("Ingresó a parámetros calificación de cartera");
		$calificaciones = ParametroCalificacionCartera::entidadId()->get();
		return view('creditos.parametroCalificacionCartera.index')->withCalificaciones($calificaciones);
	}

	public function store(CreateOrUpdateParametroCalificacionCarteraRequest $request) {
		$this->log("Creó o actualizó un parámetro calificación de cartera con los siguientes parámetros " . json_encode($request->all()), "ACTUALIZAR");
		$calificacion = ParametroCalificacionCartera::entidadId()->tipoCartera($request->tipoCartera)->calificacion($request->calificacion)->first();
		if(is_null($calificacion)) {
			$calificacion = new ParametroCalificacionCartera;
			$calificacion->entidad_id = $this->getEntidad()->id;
			$calificacion->tipo_cartera = $request->tipoCartera;
			$calificacion->calificacion = $request->calificacion;
		}
		$calificacion->dias_desde = $request->desde;
		$calificacion->dias_hasta = $request->hasta;
		$calificacion->save();
		return response()->json($calificacion);
	}

	/**
	 * Define las rutas de la calificación de cartera
	 * @return type
	 */
	public static function routes() {
		Route::get('parametrosCalificacionCartera', 'Creditos\ParametrosCalificacionCarteraController@index');
		Route::post('parametrosCalificacionCartera', 'Creditos\ParametrosCalificacionCarteraController@store');
	}
}
