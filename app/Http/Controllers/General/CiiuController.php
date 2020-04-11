<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Log;
use App\Models\General\Ciiu;

class CiiuController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		//$this->middleware('verMenu')->except(['dv']);
	}

	public function ciiu(Request $request) {
		$request->validate([
				'q'		=> 'nullable|string|min:1|max:50',
				'id'	=> [
							'bail',
							'nullable',
							'exists:sqlsrv.general.ciius,id,deleted_at,NULL',
						]
		]);
		if(!empty($request->q)) {
			$ciius = Ciiu::clase()->search($request->q)->limit(50)->get();
		}
		elseif(!empty($request->id)) {
			$ciius = Ciiu::id($request->id)->take(1)->get();
		}
		else {
			$ciius = Ciiu::clase()->take(50)->get();
		}
		$resultado = array('total_count' => $ciius->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($ciius as $ciiu) {
			$item = array('id' => $ciiu->id, 'text' => $ciiu->clase . ' - ' . $ciiu->descripcion);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public static function routes() {
		Route::get('ciiu', 'General\CiiuController@ciiu');
	}
}
