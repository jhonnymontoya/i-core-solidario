<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contabilidad\CausaAnulacionMovimiento\CreateCausaAnulacionMovimientoRequest;
use App\Http\Requests\Contabilidad\CausaAnulacionMovimiento\EditCausaAnulacionMovimientoRequest;
use App\Models\Contabilidad\CausaAnulacionMovimiento;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

/**
 * Controla las peticiones y la lógica de las causas de anulación para movimientos contables
 */
class CausasAnulacionMovimientosController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$causasAnulacionMovimientos = CausaAnulacionMovimiento::entidadId()->search($request->name)->activa($request->estado)->paginate();										
		return view('contabilidad.causasAnulacionMovimientos.index')->withCausasAnulacion($causasAnulacionMovimientos);
	}

	public function create() {
		return view('contabilidad.causasAnulacionMovimientos.create');
	}

	public function store(CreateCausaAnulacionMovimientoRequest $request) {
		$causa = new CausaAnulacionMovimiento;

		$causa->entidad_id = $this->getEntidad()->id;
		$causa->fill($request->all());

		$causa->save();

		Session::flash('message', 'Se ha creado la causa para anulación de movimiento \'' . $causa->nombre . '\'');
		return redirect('causaAnulacionMovimiento');
	}

	public function edit(CausaAnulacionMovimiento $obj) {
		return view('contabilidad.causasAnulacionMovimientos.edit')->withCausa($obj);
	}

	public function update(EditCausaAnulacionMovimientoRequest $request, CausaAnulacionMovimiento $obj) {
		$obj->fill($request->all());
		$obj->save();
		Session::flash('message', 'Se ha actualizado la causa para anulación de movimiento \'' . $obj->nombre . '\'');
		return redirect('causaAnulacionMovimiento');
	}

	public static function routes() {
		Route::get('causaAnulacionMovimiento', 'Contabilidad\CausasAnulacionMovimientosController@index');
		Route::get('causaAnulacionMovimiento/create', 'Contabilidad\CausasAnulacionMovimientosController@create');
		Route::post('causaAnulacionMovimiento', 'Contabilidad\CausasAnulacionMovimientosController@store');
		Route::get('causaAnulacionMovimiento/{obj}/edit', 'Contabilidad\CausasAnulacionMovimientosController@edit')->name('causaAnulacionMovimientoEdit');
		Route::put('causaAnulacionMovimiento/{obj}', 'Contabilidad\CausasAnulacionMovimientosController@update');
	}
}
