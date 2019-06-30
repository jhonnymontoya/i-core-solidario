<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;

use App\Models\General\TipoIdentificacion;

use App\Http\Requests\General\TipoIdentificacion\CreateTipoIdentificacionRequest;
use App\Http\Requests\General\TipoIdentificacion\EditTipoIdentificacionRequest;

class TipoIdentificacionController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$tiposIdentificacion = TipoIdentificacion::search($request->get('name'))
								->activo($request->get('estado'))
								->orderBy('esta_activo', 'desc')
								->orderBy('nombre', 'asc')
								->paginate();

		return view('general.tipoidentificacion.index')
			->withTiposIdentificacion($tiposIdentificacion);
	}

	public function create() {
		return view('general.tipoidentificacion.create');
	}

	public function store(CreateTipoIdentificacionRequest $request) {
		$tipoIdentificacion = TipoIdentificacion::create($request->all());
		Session::flash('message', 'Se ha creado el tipo de identificación \''. $tipoIdentificacion->nombre . '\'');
		return redirect('tipoIdentificacion');
	}

	public function edit(TipoIdentificacion $obj) {
		return view('general.tipoidentificacion.edit')->withTipoIdentificacion($obj);
	}

	public function update(EditTipoIdentificacionRequest $request, TipoIdentificacion $obj) {
		$obj->fill($request->all());
		$obj->save();
		Session::flash('message', 'Se ha actualizado el tipo de identificación \''. $obj->nombre . '\'');
		return redirect('tipoIdentificacion');
	}

	public static function routes() {
		Route::get('tipoIdentificacion', 'General\TipoIdentificacionController@index');
		Route::get('tipoIdentificacion/create', 'General\TipoIdentificacionController@create');
		Route::post('tipoIdentificacion', 'General\TipoIdentificacionController@store');
		Route::get('tipoIdentificacion/{obj}/edit', 'General\TipoIdentificacionController@edit')->name('tipoIdentificacionEdit');
		Route::put('tipoIdentificacion/{obj}', 'General\TipoIdentificacionController@update');
	}
}
