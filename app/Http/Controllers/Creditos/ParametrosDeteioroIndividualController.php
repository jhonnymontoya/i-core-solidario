<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\ParametrosDeterioroIndividual\CreateParametroDeterioroIndividualRequest;
use App\Http\Requests\Creditos\ParametrosDeterioroIndividual\SelectParametrosDeterioroIndividualRequest;
use App\Models\Creditos\ParametroDeterioroIndividual;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Route;

class ParametrosDeteioroIndividualController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index() {
		$this->log("Ingresó a parametros deterioro individual");
		$parametros = ParametroDeterioroIndividual::entidadId()->tipoCartera('CONSUMO')->clase('CAPITAL')->get();
		return view('creditos.parametroDeterioroIndividual.index')->withParametros($parametros);
	}

	public function store(CreateParametroDeterioroIndividualRequest $request) {
		$this->log("Creó un parametro deterioro individual con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$parametro = new ParametroDeterioroIndividual;
		$parametro->entidad_id = $this->getEntidad()->id;
		$parametro->tipo_cartera = $request->tipo_cartera;
		$parametro->clase = $request->clase;
		$parametro->dias_desde = $request->dias_desde;
		$parametro->dias_hasta = $request->dias_hasta;
		$parametro->deterioro = $request->deterioro;

		$parametro->save();

		return response()->json($parametro);
	}

	public function parametros(SelectParametrosDeterioroIndividualRequest $request) {
		$parametros = ParametroDeterioroIndividual::entidadId()->tipoCartera($request->tipo_cartera)->clase($request->clase)->get();
		return response()->json($parametros);
	}

	public function delete(ParametroDeterioroIndividual $obj) {
		$this->objEntidad($obj);
		$obj->delete();
		return response()->json(["ok" => true]);
	}

	public static function routes() {
		Route::get('parametrosDeterioroIndividual', 'Creditos\ParametrosDeteioroIndividualController@index');
		Route::post('parametrosDeterioroIndividual', 'Creditos\ParametrosDeteioroIndividualController@store');
		Route::get('parametrosDeterioroIndividual/parametros', 'Creditos\ParametrosDeteioroIndividualController@parametros');
		Route::delete('parametrosDeterioroIndividual/{obj}', 'Creditos\ParametrosDeteioroIndividualController@delete');
	}
}
