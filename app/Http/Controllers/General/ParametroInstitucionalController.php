<?php

namespace App\Http\Controllers\General;

use Route;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\General\ParametroInstitucional;
use App\Http\Requests\General\ParametroInstitucional\EditParametroInstitucionalRequest;

class ParametroInstitucionalController extends Controller
{
    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$parametros = ParametroInstitucional::entidadId()
			->modulo($request->modulo)
			->search($request->name)
			->paginate();

		$modulos = [
			"AHORROS" => "Ahorros",
		    "CRÉDITOS" => "Créditos",
		    "CONTABILIDAD" => "Contabilidad",
		    "CONTROL Y VIGILANCIA" => "Control y Vigilancia",
		    "CONVENIOS" => "Convenios",
		    "GENERAL" => "General",
		    "IMPUESTOS" => "Impuestos",
		    "NÓMINA" => "Nómina",
		    "RECAUDOS MASIVOS" => "Recaudos Masivos",
		    "SOCIOS" => "Socios",
		    "TESORERÍA" => "Tesorería"
		];
		return view('general.parametroInstitucional.index')
			->withParametros($parametros)
			->withModulos($modulos);
	}

	public function edit(ParametroInstitucional $obj) {
		return view('general.parametroInstitucional.edit')->withParametro($obj);
	}

	public function update(EditParametroInstitucionalRequest $request, ParametroInstitucional $obj) {
		if($obj->tipo_parametro == 'VALOR') {
			$obj->valor = $request->valor;
		}
		elseif($obj->tipo_parametro == 'INDICADOR') {
			$obj->indicador = $request->indicador;
		}
		else {
			$obj->indicador = $request->indicador;
			$obj->valor = $request->valor;
		}
		$obj->save();
		Session::flash('message', 'Se ha actualizado el parámetro institucional');
		return redirect('parametrosInstitucionales');
	}

	public static function routes() {
		Route::get('parametrosInstitucionales', 'General\ParametroInstitucionalController@index');
		Route::get('parametrosInstitucionales/{obj}/edit', 'General\ParametroInstitucionalController@edit')->name('parametroInstitucionalEdit');
		Route::put('parametrosInstitucionales/{obj}', 'General\ParametroInstitucionalController@update');
	}
}
