<?php

namespace App\Http\Controllers\Contabilidad;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;

use App\Models\Contabilidad\Modulo;

use App\Http\Requests\Contabilidad\Modulo\CreateModuloRequest;
use App\Http\Requests\Contabilidad\Modulo\EditModuloRequest;

class ModuloController extends Controller
{
    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$modulos = Modulo::search($request->get('name'))
								->activo($request->get('estado'))
								->orderBy('esta_activo', 'desc')
								->orderBy('nombre', 'asc')
								->paginate();
		return view('contabilidad.modulo.index')
			->withModulos($modulos);
	}

	public function create() {
		return view('contabilidad.modulo.create');
	}

	public function store(CreateModuloRequest $request) {
		$modulo = Modulo::create($request->all());
		Session::flash('message', 'Se ha creado el módulo \''. $modulo->nombre . '\'');

		return redirect('modulo');
	}

	public function edit(Modulo $obj) {
		return view('contabilidad.modulo.edit')->withModulo($obj);
	}

	public function update(EditModuloRequest $request, Modulo $obj) {
		$obj->nombre					= $request->nombre;
		$obj->activo					= $request->activo;
		$obj->save();

		Session::flash('message', 'Se ha actualizado el módulo \''. $obj->nombre . '\'');
		return redirect('modulo');
	}

	public static function routes() {
		Route::get('modulo', 'Contabilidad\ModuloController@index');
		Route::get('modulo/create', 'Contabilidad\ModuloController@create');
		Route::post('modulo', 'Contabilidad\ModuloController@store');
		Route::get('modulo/{obj}/edit', 'Contabilidad\ModuloController@edit')->name('moduloEdit');
		Route::put('modulo/{obj}', 'Contabilidad\ModuloController@update');
	}
}
