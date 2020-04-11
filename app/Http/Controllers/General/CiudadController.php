<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\General\Ciudad;

class CiudadController extends Controller
{
	public function __construct() {
	}

	////API

	public function ciudad(Request $request) {
		if(!empty($request->q)) {
			$ciudades = Ciudad::with('departamento')->search($request->q)->orderBy('nombre')->limit(50)->get();
		}
		elseif(!empty($request->id)) {
			$ciudades = Ciudad::with('departamento')->id($request->id)->take(1)->get();
		}
		else {
			$ciudades = Ciudad::with('departamento')->limit(50)->orderBy('nombre')->get();
		}

		$resultado = array('total_count' => $ciudades->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($ciudades as $ciudad) {
			$item = array('id' => $ciudad->id, 'text' => $ciudad->nombre . ' - ' . $ciudad->departamento->nombre);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}
}
