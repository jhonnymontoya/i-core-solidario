<?php

namespace App\Http\Controllers\General;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\General\Profesion;

class ProfesionController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin')->except(['getProfesion']);
		$this->middleware('verEnt')->except(['getProfesion']);
		$this->middleware('verMenu')->except(['getProfesion']);
	}

	////API

	public function getProfesion(Request $request) {
		if(!empty($request->q)) {
			$profesiones = Profesion::search($request->q)->orderBy('nombre')->limit(20)->get();
		}
		elseif(!empty($request->id)) {
			$profesiones = Profesion::id($request->id)->take(1)->get();
		}
		else {
			$profesiones = Profesion::take(20)->orderBy('nombre')->get();
		}
		$resultado = array('total_count' => $profesiones->count(), 'incomplete_results' => false);
		$resultado['items'] = array();
		foreach($profesiones as $profesion) {
			$item = array('id' => $profesion->id, 'text' => $profesion->nombre);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public static function routes() {
	}
}
