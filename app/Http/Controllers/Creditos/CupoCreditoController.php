<?php

namespace App\Http\Controllers\Creditos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\General\ParametroInstitucional;

use App\Http\Requests\Creditos\CupoCredito\EditCupoCreditoRequest;

class CupoCreditoController extends Controller
{
    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$modalidades = ModalidadAhorro::entidadId()->whereEsReintegrable(true)->paginate();
		$parametroInstitucional = ParametroInstitucional::entidadId()->codigo('CR001')->first();

		if($parametroInstitucional == null) {
			Session::flash('error', 'Parámetro institucional CR001 no encontrado');
			return redirect('parametrosInstitucionales');
		}
		return view('creditos.cupoCredito.index')
					->withModalidades($modalidades)
					->withParametro($parametroInstitucional);
	}

	public function edit(ModalidadAhorro $obj) {
		return view('creditos.cupoCredito.edit')->withModalidad($obj);
	}

	public function update(EditCupoCreditoRequest $request, ModalidadAhorro $obj) {
		$obj->apalancamiento_cupo = $request->apalancamiento_cupo;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el parámetro de cupo para la modalidad \'' . $obj->codigo . ' - ' . $obj->nombre . '\'');

		return redirect('cupoCredito');
	}

	public static function routes() {
		Route::get('cupoCredito', 'Creditos\CupoCreditoController@index');		
		Route::get('cupoCredito/{obj}/edit', 'Creditos\CupoCreditoController@edit')->name('cupoCreditoEdit');
		Route::put('cupoCredito/{obj}', 'Creditos\CupoCreditoController@update');
	}
}
