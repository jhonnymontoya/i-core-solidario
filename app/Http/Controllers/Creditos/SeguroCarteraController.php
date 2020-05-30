<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\SeguroCartera\CreateSeguroCarteraRequest;
use App\Http\Requests\Creditos\SeguroCartera\EditSeguroCarteraRequest;
use App\Models\Creditos\Modalidad;
use App\Models\Creditos\SeguroCartera;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class SeguroCarteraController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$request->validate([
			'name'	=> 'bail|nullable|string|max:150',
		]);
		$segurosCartera = SeguroCartera::entidadId()->search($request->name)->paginate();
		return view('creditos.seguroCartera.index')->withSegurosCartera($segurosCartera);
	}

	public function create() {
		return view('creditos.seguroCartera.create');
	}

	public function store(CreateSeguroCarteraRequest $request) {
		$seguroCartera = new SeguroCartera;
		$seguroCartera->fill($request->all());
		$seguroCartera->entidad_id = $this->getEntidad()->id;
		$seguroCartera->save();

		Session::flash('message', 'Se ha creado el seguro para cartera \'' . $seguroCartera->nombre . '\'');
		return redirect('seguroCartera');
	}

	public function edit(SeguroCartera $obj) {
		$this->objEntidad($obj, 'No esta autorizado a editar el seguro de cartera');
		return view('creditos.seguroCartera.edit')->withSeguroCartera($obj);
	}

	public function update(EditSeguroCarteraRequest $request, SeguroCartera $obj) {
		$this->objEntidad($obj, 'No esta autorizado a editar el seguro de cartera');
		$obj->aseguradora_tercero_id	= $request->aseguradora_tercero_id;
		$obj->nombre					= $request->nombre;
		$obj->base_prima				= $request->base_prima;
		$obj->tasa_mes					= $request->tasa_mes;
		$obj->esta_activo				= $request->esta_activo;
		$obj->save();

		Session::flash('message', 'Se ha actualizado el seguro para cartera \'' . $obj->nombre . '\'');
		return redirect('seguroCartera');
	}

	public function modalidades(SeguroCartera $obj) {
		$this->objEntidad($obj, 'No esta autorizado a editar el seguro de cartera');
		$modalidades = Modalidad::entidadId()->orderBy('nombre')->get();
		return view('creditos.seguroCartera.modalidades')->withSeguroCartera($obj)->withModalidades($modalidades);
	}

	public function asociar(SeguroCartera $obj, Modalidad $modalidad) {
		$this->objEntidad($obj, 'No esta autorizado a editar el seguro de cartera');
		$this->objEntidad($modalidad, 'No esta autorizado a editar el seguro de cartera');
		if($modalidad->segurosCartera->count()) {
			if($modalidad->segurosCartera[0]->id != $obj->id) {
				$modalidad->segurosCartera()->toggle($modalidad->segurosCartera[0]);
				$estado = $obj->modalidades->count() == 1 ? true : false;
				return response()->json(["asociado" => $estado]);
			}
		}
		$obj->modalidades()->toggle($modalidad);
		$estado = $modalidad->segurosCartera->count() == 1 ? false : true;
		return response()->json(["asociado" => $estado]);
	}

	public static function routes() {
		Route::get('seguroCartera', 'Creditos\SeguroCarteraController@index');
		Route::get('seguroCartera/create', 'Creditos\SeguroCarteraController@create');
		Route::post('seguroCartera', 'Creditos\SeguroCarteraController@store');
		Route::get('seguroCartera/{obj}/edit', 'Creditos\SeguroCarteraController@edit')->name('seguroCarteraEdit');
		Route::put('seguroCartera/{obj}', 'Creditos\SeguroCarteraController@update');
		Route::get('seguroCartera/{obj}/modalidades', 'Creditos\SeguroCarteraController@modalidades')->name('seguroCarteraModalidades');
		Route::get('seguroCartera/{obj}/{modalidad}', 'Creditos\SeguroCarteraController@asociar')->name('seguroCarteraAsociar');
	}
}
