<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\TipoIndicador\CreateTipoIndicadorRequest;
use App\Http\Requests\General\TipoIndicador\EditTipoIndicadorRequest;
use App\Models\General\TipoIndicador;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class TipoIndicadorController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$tiposIndicadores = TipoIndicador::entidadId()->search($request->name)->paginate();
		return view('general.tipoindicador.index')->withTiposIndicadores($tiposIndicadores);
	}

	public function create() {
		$periodicidades = array(
				'DIARIO' => 'Diario',
				'SEMANAL' => 'Semanal',
				'DECADAL' => 'Decadal',
				'CATORCENAL' => 'Catorcenal',
				'QUINCENAL' => 'Quincenal',
				'MENSUAL' => 'Mensual',
				'BIMESTRAL' => 'Bimestral',
				'TRIMESTRAL' => 'Trimestral',
				'CUATRIMESTRAL' => 'Cuatrimestral',
				'SEMESTRAL' => 'Semestral',
				'ANUAL' => 'Anual'
			);
		return view('general.tipoindicador.create')->withPeriodicidades($periodicidades);
	}

	public function store(CreateTipoIndicadorRequest $request) {
		$tipoIndicador = new TipoIndicador;
		$tipoIndicador->entidad_id = $this->getEntidad()->id;
		$tipoIndicador->fill($request->all());
		$tipoIndicador->save();
		Session::flash('message', 'Se ha creado el tipo de indicador \''. $tipoIndicador->codigo . '\'');
		return redirect('tipoIndicador');
	}

	public function edit(TipoIndicador $obj) {
		$periodicidades = array(
				'DIARIO' => 'Diario',
				'SEMANAL' => 'Semanal',
				'DECADAL' => 'Decadal',
				'CATORCENAL' => 'Catorcenal',
				'QUINCENAL' => 'Quincenal',
				'MENSUAL' => 'Mensual',
				'BIMESTRAL' => 'Bimestral',
				'TRIMESTRAL' => 'Trimestral',
				'CUATRIMESTRAL' => 'Cuatrimestral',
				'SEMESTRAL' => 'Semestral',
				'ANUAL' => 'Anual'
			);
		return view('general.tipoindicador.edit')->withPeriodicidades($periodicidades)->withTipoIndicador($obj);
	}

	public function update(TipoIndicador $obj, EditTipoIndicadorRequest $request) {
		if($obj->indicadores->count()) {
			$obj->descripcion = $request->descripcion;
		}
		else {
			$obj->fill($request->all());
		}
		$obj->save();
		Session::flash('message', 'Se ha actualizado el tipo de indicador \''. $obj->codigo . '\'');
		return redirect('tipoIndicador');
	}

	public static function routes() {
		Route::get('tipoIndicador', 'General\TipoIndicadorController@index');
		Route::get('tipoIndicador/create', 'General\TipoIndicadorController@create');
		Route::post('tipoIndicador', 'General\TipoIndicadorController@store');
		Route::get('tipoIndicador/{obj}/edit', 'General\TipoIndicadorController@edit')->name('tipoIndicadorEdit');
		Route::put('tipoIndicador/{obj}', 'General\TipoIndicadorController@update');
	}
}
