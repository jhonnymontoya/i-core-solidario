<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;
use App\Models\General\CategoriaImagen;
use App\Http\Requests\General\CategoriaImagen\CreateCategoriaImagenRequest;
use App\Http\Requests\General\CategoriaImagen\EditCategoriaImagenRequest;
/**
 * Controla las peticiones y la lógica de las categorías de imagenes institucionales
 */
class CategoriaImagenController extends Controller
{
    public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index() {
		$categoriasImagen = CategoriaImagen::orderBy('nombre', 'asc')->paginate();
		return view('general.categoriaImagen.index')->withCategoriasImagen($categoriasImagen);
	}

	public function create() {
		return view('general.categoriaImagen.create');
	}

	public function store(CreateCategoriaImagenRequest $request) {
		$categoriaImagen = CategoriaImagen::create($request->all());
		Session::flash('message', 'Se ha creado la categoría \''. $categoriaImagen->nombre . '\'');
		return redirect('categoriaImagen');
	}

	public function edit(CategoriaImagen $obj) {
		$tieneImagenes = $obj->entidades->count()?true:false;		
		return view('general.categoriaImagen.edit')->withCategoriaImagen($obj)->withTieneImagenes($tieneImagenes);
	}

	public function update(CategoriaImagen $obj, CreateCategoriaImagenRequest $request) {
		$obj->fill($request->all());
		$obj->save();
		Session::flash('message', 'Se ha actualizado la categoría \''. $obj->nombre . '\'');
		return redirect('categoriaImagen');
	}

	public static function routes() {
		Route::get('categoriaImagen', 'General\CategoriaImagenController@index');
		Route::get('categoriaImagen/create', 'General\CategoriaImagenController@create');
		Route::post('categoriaImagen', 'General\CategoriaImagenController@store');
		Route::get('categoriaImagen/{obj}/edit', 'General\CategoriaImagenController@edit')->name('categoriaImagenEdit');
		Route::put('categoriaImagen/{obj}', 'General\CategoriaImagenController@update');
	}
}
