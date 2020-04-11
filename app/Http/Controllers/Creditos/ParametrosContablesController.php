<?php

namespace App\Http\Controllers\Creditos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

use App\Models\Creditos\ParametroContable;

class ParametrosContablesController extends Controller
{
    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$parametrosContables = ParametroContable::entidadId()->get();
		return view('creditos.parametrosContables.index');
	}

	public function cuentas(Request $request) {
		Validator::make($request->all(), [
			'tipo_cartera'	=> 'bail|required|string|in:CONSUMO,VIVIENDA,COMERCIAL,MICROCREDITO',
			'tipo_garantia'	=> 'bail|required|string|in:GARANTIA ADMISIBLE (REAL) CON LIBRANZA,OTRAS GARANTIAS (PERSONAL) CON LIBRANZA,GARANTIA ADMISIBLE (REAL) SIN LIBRANZA,OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA',
		])->validate();

		$parametrosContables = ParametroContable::entidadId()
									->whereTipoCartera($request->tipo_cartera)
									->whereTipoGarantia($request->tipo_garantia)
									->get();
		$datos = array(
						'capital'				=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
						'interesIngreso'		=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
						'interesCobrar'			=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
						'interesAnticipados'	=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
						'deterioroCapital'		=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
						'deterioroIntereses'	=> [
														'A' => ['id' => '', 'cuenta' => ''],
														'B' => ['id' => '', 'cuenta' => ''],
														'C' => ['id' => '', 'cuenta' => ''],
														'D' => ['id' => '', 'cuenta' => ''],
														'E' => ['id' => '', 'cuenta' => '']
													],
				);

		foreach($parametrosContables as $parametro) {
			switch ($parametro->categoria_clasificacion) {
				case 'A':
					$datos['capital']['A']['id'] = $parametro->id;
					$datos['capital']['A']['cuenta'] = $parametro->cuentaCapital->codigo;

					$datos['interesIngreso']['A']['id'] = $parametro->id;
					$datos['interesIngreso']['A']['cuenta'] = $parametro->cuentaInteresesIngreso->codigo;

					$datos['interesCobrar']['A']['id'] = $parametro->id;
					$datos['interesCobrar']['A']['cuenta'] = $parametro->cuentaInteresesPorCobrar->codigo;

					$datos['interesAnticipados']['A']['id'] = $parametro->id;
					$datos['interesAnticipados']['A']['cuenta'] = $parametro->cuentaInteresesAnticipados->codigo;

					$datos['deterioroCapital']['A']['id'] = $parametro->id;
					$datos['deterioroCapital']['A']['cuenta'] = $parametro->cuentaDeterioroCapital->codigo;

					$datos['deterioroIntereses']['A']['id'] = $parametro->id;
					$datos['deterioroIntereses']['A']['cuenta'] = $parametro->cuentaDeterioroIntereses->codigo;
					break;
				case 'B':
					$datos['capital']['B']['id'] = $parametro->id;
					$datos['capital']['B']['cuenta'] = $parametro->cuentaCapital->codigo;

					$datos['interesIngreso']['B']['id'] = $parametro->id;
					$datos['interesIngreso']['B']['cuenta'] = $parametro->cuentaInteresesIngreso->codigo;

					$datos['interesCobrar']['B']['id'] = $parametro->id;
					$datos['interesCobrar']['B']['cuenta'] = $parametro->cuentaInteresesPorCobrar->codigo;

					$datos['interesAnticipados']['B']['id'] = $parametro->id;
					$datos['interesAnticipados']['B']['cuenta'] = $parametro->cuentaInteresesAnticipados->codigo;

					$datos['deterioroCapital']['B']['id'] = $parametro->id;
					$datos['deterioroCapital']['B']['cuenta'] = $parametro->cuentaDeterioroCapital->codigo;

					$datos['deterioroIntereses']['B']['id'] = $parametro->id;
					$datos['deterioroIntereses']['B']['cuenta'] = $parametro->cuentaDeterioroIntereses->codigo;
					break;
				case 'C':
					$datos['capital']['C']['id'] = $parametro->id;
					$datos['capital']['C']['cuenta'] = $parametro->cuentaCapital->codigo;

					$datos['interesIngreso']['C']['id'] = $parametro->id;
					$datos['interesIngreso']['C']['cuenta'] = $parametro->cuentaInteresesIngreso->codigo;

					$datos['interesCobrar']['C']['id'] = $parametro->id;
					$datos['interesCobrar']['C']['cuenta'] = $parametro->cuentaInteresesPorCobrar->codigo;

					$datos['interesAnticipados']['C']['id'] = $parametro->id;
					$datos['interesAnticipados']['C']['cuenta'] = $parametro->cuentaInteresesAnticipados->codigo;

					$datos['deterioroCapital']['C']['id'] = $parametro->id;
					$datos['deterioroCapital']['C']['cuenta'] = $parametro->cuentaDeterioroCapital->codigo;

					$datos['deterioroIntereses']['C']['id'] = $parametro->id;
					$datos['deterioroIntereses']['C']['cuenta'] = $parametro->cuentaDeterioroIntereses->codigo;
					break;
				case 'D':
					$datos['capital']['D']['id'] = $parametro->id;
					$datos['capital']['D']['cuenta'] = $parametro->cuentaCapital->codigo;

					$datos['interesIngreso']['D']['id'] = $parametro->id;
					$datos['interesIngreso']['D']['cuenta'] = $parametro->cuentaInteresesIngreso->codigo;

					$datos['interesCobrar']['D']['id'] = $parametro->id;
					$datos['interesCobrar']['D']['cuenta'] = $parametro->cuentaInteresesPorCobrar->codigo;

					$datos['interesAnticipados']['D']['id'] = $parametro->id;
					$datos['interesAnticipados']['D']['cuenta'] = $parametro->cuentaInteresesAnticipados->codigo;

					$datos['deterioroCapital']['D']['id'] = $parametro->id;
					$datos['deterioroCapital']['D']['cuenta'] = $parametro->cuentaDeterioroCapital->codigo;

					$datos['deterioroIntereses']['D']['id'] = $parametro->id;
					$datos['deterioroIntereses']['D']['cuenta'] = $parametro->cuentaDeterioroIntereses->codigo;
					break;
				case 'E':
					$datos['capital']['E']['id'] = $parametro->id;
					$datos['capital']['E']['cuenta'] = $parametro->cuentaCapital->codigo;

					$datos['interesIngreso']['E']['id'] = $parametro->id;
					$datos['interesIngreso']['E']['cuenta'] = $parametro->cuentaInteresesIngreso->codigo;

					$datos['interesCobrar']['E']['id'] = $parametro->id;
					$datos['interesCobrar']['E']['cuenta'] = $parametro->cuentaInteresesPorCobrar->codigo;

					$datos['interesAnticipados']['E']['id'] = $parametro->id;
					$datos['interesAnticipados']['E']['cuenta'] = $parametro->cuentaInteresesAnticipados->codigo;

					$datos['deterioroCapital']['E']['id'] = $parametro->id;
					$datos['deterioroCapital']['E']['cuenta'] = $parametro->cuentaDeterioroCapital->codigo;

					$datos['deterioroIntereses']['E']['id'] = $parametro->id;
					$datos['deterioroIntereses']['E']['cuenta'] = $parametro->cuentaDeterioroIntereses->codigo;
					break;
			}
			
		}
		return response()->json($datos);
	}

	public function edit(ParametroContable $obj) {
		//edit
	}

	public function update(Request $request, ParametroContable $obj) {
		//edit update
	}

	public static function routes() {
		Route::get('parametrosContablesCreditos', 'Creditos\ParametrosContablesController@index');		
		Route::get('parametrosContablesCreditos/cuentas', 'Creditos\ParametrosContablesController@cuentas');		
		Route::get('parametrosContablesCreditos/{obj}/edit', 'Creditos\ParametrosContablesController@edit')->name('parametrosContablesCreditosEdit');
		Route::put('parametrosContablesCreditos/{obj}', 'Creditos\ParametrosContablesController@update');
	}
}
