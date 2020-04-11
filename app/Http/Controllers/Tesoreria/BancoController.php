<?php

namespace App\Http\Controllers\Tesoreria;

use Validator;
use Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tesoreria\Banco;

class BancoController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu')->except(['getBanco']);
	}

	////API

	/**
	 * Obtiene una lista de los bancos por la entidad
	 * @param Request $request 
	 * @return type
	 */
	public function getBanco(Request $request) {
		$request->validate([
			'q'		=> 'bail|nullable|string|max:100',
			'id'	=> 'bail|nullable|integer|min:1'
		]);
		if(!empty($request->q)) {
			$bancos = Banco::entidadBanco()->activo()->search($request->q)->limit(20)->get();
		}
		elseif(!empty($request->id)) {
			$bancos = Banco::entidadBanco()->activo()->id($request->id)->take(1)->get();
		}
		else {
			$bancos = Banco::entidadBanco()->activo()->take(20)->get();
		}

		$resultado = array('total_count' => $bancos->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($bancos as $banco) {
			$item = array('id' => $banco->id, 'text' => $banco->codigo . ' - ' . $banco->nombre);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public static function routes() {
		Route::get('banco/listar', 'Tesoreria\BancoController@getBanco');
	}
}
