<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Route;
use App\Models\Sistema\Menu;
use App\Http\Requests\Sistema\Menu\CreateMenuRequest;
use App\Http\Requests\Sistema\Menu\EditMenuRequest;

class MenuController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index() {
		//$menus = Menu::paginate();
		$menus = Menu::padres()
					->orderBy('orden')
					->with(['perfiles', 'hijos' => function($query){
						$query->orderBy('orden')->with('hijos');
					}])
					->get();
		return view('sistema.menu.index')->withMenus($menus);
	}

	public function create() {
		$lista = Menu::listar();
		return view('sistema.menu.create')->withLista($lista);
	}

	public function store(CreateMenuRequest $request) {
		$menu = new Menu;

		if($request->has('padre')) {
			$padre = Menu::find($request->padre);
			$menu->padre()->associate($padre);
		}
		$menu->nombre = $request->nombre;
		if($request->has('ruta')) {
			$menu->ruta = $request->ruta;
		}
		if($request->has('icono')) {
			$menu->pre_icon = $request->icono;
		}
		$menu->save();
		Session::flash('message', 'Se ha creado el menú \''. $menu->nombre . '\'');
		return redirect('menu');
	}

	public function edit(Menu $obj) {
		$lista = Menu::listar();
		return view('sistema.menu.edit')->withLista($lista)->withMenu($obj);
	}

	public function update(EditMenuRequest $request, Menu $obj) {
		if($request->has('padre')) {
			$padre = Menu::find($request->padre);
			if($padre->id != $obj->id) $obj->padre()->associate($padre);
		}
		else {
			if($obj->padre != null)$obj->padre()->dissociate();
		}
		$obj->nombre = $request->nombre;
		if($request->has('ruta')) {
			$obj->ruta = $request->ruta;
		}
		else {
			$obj->ruta = null;
		}
		if($request->has('icono')) {
			$obj->pre_icon = $request->icono;
		}
		else {
			$obj->pre_icon = null;
		}
		Session::flash('message', 'Se ha actualizado el menú \''. $obj->nombre . '\'');
		$obj->save();
		return redirect('menu');
	}

	public static function routes() {
		Route::get('menu', 'Sistema\MenuController@index');
		Route::get('menu/create', 'Sistema\MenuController@create');
		Route::post('menu', 'Sistema\MenuController@store');
		Route::get('menu/{obj}/edit', 'Sistema\MenuController@edit')->name('menuEdit');
		Route::put('menu/{obj}', 'Sistema\MenuController@update');
	}
}
