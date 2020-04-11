<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\CUpoCredito\EditCupoCreditoSDATRequest;
use App\Http\Requests\Creditos\CupoCredito\EditCupoCreditoRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Ahorros\TipoSDAT;
use App\Models\General\ParametroInstitucional;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;
use Route;
use Validator;

class CupoCreditoController extends Controller
{
	use FonadminTrait;

    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log("Ingresó a cupos de crédito");
		$modalidades = ModalidadAhorro::entidadId()->whereEsReintegrable(true)->paginate();
		$parametroInstitucional = ParametroInstitucional::entidadId()->codigo('CR001')->first();
		$sdats = TipoSDAT::entidadId()->activo()->paginate();

		if($parametroInstitucional == null) {
			Session::flash('error', 'Parámetro institucional CR001 no encontrado');
			return redirect('parametrosInstitucionales');
		}
		return view('creditos.cupoCredito.index')
			->withModalidades($modalidades)
			->withParametro($parametroInstitucional)
			->withSdats($sdats);
	}

	public function edit(ModalidadAhorro $obj) {
		$msg = "Ingresó a editar las veces de apalancamiento de la modalidad '%s'";
		$this->log(sprintf($msg, $obj->cod));
		return view('creditos.cupoCredito.edit')->withModalidad($obj);
	}

	public function update(EditCupoCreditoRequest $request, ModalidadAhorro $obj) {
		$this->log("Actualizó el cupo de crédito con los siguientes parámetros " . json_encode($request->all()), "ACTUALIZAR");
		$obj->apalancamiento_cupo = $request->apalancamiento_cupo;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el parámetro de cupo para la modalidad \'' . $obj->codigo . ' - ' . $obj->nombre . '\'');

		return redirect('cupoCredito');
	}

	public function editSDAT(TipoSDAT $obj) {
		$msg = "Ingresó a editar las veces de apalancamiento del SDAT '%s'";
		$this->log(sprintf($msg, $obj->cod));
		return view('creditos.cupoCredito.editSDAT')->withSdat($obj);
	}

	public function updateSDAT(EditCupoCreditoSDATRequest $request, TipoSDAT $obj) {
		$this->log("Actualizó el cupo de crédito con los siguientes parámetros " . json_encode($request->all()), "ACTUALIZAR");
		$obj->apalancamiento_cupo = $request->apalancamiento_cupo;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el parámetro de cupo para el tipo SDAT \'' . $obj->codigo . ' - ' . $obj->nombre . '\'');

		return redirect('cupoCredito');
	}

	public static function routes() {
		Route::get('cupoCredito', 'Creditos\CupoCreditoController@index');		
		Route::get('cupoCredito/{obj}/edit', 'Creditos\CupoCreditoController@edit')->name('cupoCreditoEdit');
		Route::put('cupoCredito/{obj}', 'Creditos\CupoCreditoController@update');
		Route::get('cupoCredito/{obj}/editSDAT', 'Creditos\CupoCreditoController@editSDAT')->name('cupoCredito.get.edit.sdat');
		Route::put('cupoCredito/{obj}/sdat', 'Creditos\CupoCreditoController@updateSDAT')->name('cupoCredito.put.edit.sdat');
	}
}
