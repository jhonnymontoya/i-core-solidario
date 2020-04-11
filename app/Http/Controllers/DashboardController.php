<?php

namespace App\Http\Controllers;

use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class DashboardController extends Controller
{
	use FonadminTrait;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		if(Auth::guest()) {
			return redirect('login');
		}
		else {
			return redirect('dashboard');
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function dashboard() {
		$comparativoSocios = $this->obtenerComparativoSocios();
		$afiliacionesRecientes = Socio::entidad()
			->with('tercero')
			->orderBy('fecha_afiliacion', 'desc')
			->take(12)
			->get();
		return view('dashboard')
			->withComparativoSocios($comparativoSocios)
			->withUltimosAfiliados($afiliacionesRecientes);
	}

	/**
	 * Obtiene el comparativo de socios
	 * @return type
	 */
	private function obtenerComparativoSocios() {
		$consulta = "exec socios.sp_comparativo_socios_activos ?";
		$consulta = DB::select($consulta, [$this->getEntidad()->id]);
		$comparativo = ['labels' => [], 'DSAnterior' => [], 'DSActual' => []];
		if(empty($consulta))return $comparativo;
		foreach ($consulta as $value) {
			array_push($comparativo['labels'], $value->mes);
			array_push($comparativo['DSAnterior'], $value->anterior);
			array_push($comparativo['DSActual'], $value->actual);
		}
		return $comparativo;
	}

	/**
	 * Sirve las rutas del controlador
	 * @return type
	 */
	public static function routes() {
		Route::get('/', 'DashboardController@index');
		Route::get('dashboard', 'DashboardController@dashboard');
	}
}
