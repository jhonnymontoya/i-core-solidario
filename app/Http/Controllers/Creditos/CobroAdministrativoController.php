<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\CobroAdministrativo\CreateCobroAdministrativoRequest;
use App\Http\Requests\Creditos\CobroAdministrativo\CreateCondicionRequest;
use App\Http\Requests\Creditos\CobroAdministrativo\DeleteCondicionRequest;
use App\Http\Requests\Creditos\CobroAdministrativo\EditCobroAdministrativoRequest;
use App\Models\Creditos\CobroAdministrativo;
use App\Models\Creditos\CondicionCobroAdministrativoRango;
use App\Models\Creditos\Modalidad;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class CobroAdministrativoController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a cobros administrativos", $request);
		$request->validate(['name'	=> 'bail|nullable|string|max:50']);
		$cobros = CobroAdministrativo::entidadId()->search($request->name)->orderBy('codigo')->paginate();
		return view('creditos.cobroAdministrativo.index')->withCobrosAdministrativos($cobros);
	}

	public function create() {
		$this->log("Ingresó a crear cobro administrativo");
		return view('creditos.cobroAdministrativo.create');
	}

	public function store(CreateCobroAdministrativoRequest $request) {
		$this->log("Creó cobro administrativo con los siguientes parámetros " . json_encode($request->all()), 'CREAR');
		$cobro = new CobroAdministrativo;
		$cobro->fill($request->all());
		$cobro->entidad_id = $this->getEntidad()->id;
		$cobro->save();
		Session::flash("message", 'Se ha creado el cobro administrativo');
		return redirect()->route('cobrosAdministrativos.edit', $cobro->id);
	}

	public function edit(CobroAdministrativo $obj) {
		$this->log("Ingresó a edición del cobro administrativo $obj->nombre");
		$this->objEntidad($obj, "No tiene autorización para editar el cobro administrativo");
		return view('creditos.cobroAdministrativo.edit')->withCobro($obj);
	}

	public function update(CobroAdministrativo $obj, EditCobroAdministrativoRequest $request) {
		$this->log("Actualizó cobro administrativo '$obj->nombre'", "ACTUALIZAR");
		$this->objEntidad($obj, "No tiene autorización para editar el cobro administrativo");
		$obj->fill($request->all());
		if($obj->efecto == 'ADICIONCREDITO')$obj->base_cobro = 'VALORCREDITO';
		$obj->save();
		Session::flash("message", 'Se ha actualizado el cobro administrativo');
		return redirect()->route('cobrosAdministrativos.edit', $obj->id);
	}

	//
	public function guardarCondicion(CobroAdministrativo $obj, CreateCondicionRequest $request) {
		$this->objEntidad($obj, "No tiene autorización para editar el cobro administrativo");
		$condicion = new CondicionCobroAdministrativoRango;
		$condicion->cobro_administrativo_id = $obj->id;
		$condicion->condicion_desde = $request->d;
		$condicion->condicion_hasta = $request->h;
		$condicion->base_cobro = $request->bc;
		$condicion->factor_calculo = $request->fc;
		$condicion->valor = $request->v;
		if($obj->efecto == 'ADICIONCREDITO')$condicion->base_cobro = 'VALORCREDITO';
		$condicion->save();

		return response()->json($condicion->paraMostrar(), 201);
	}

	public function eliminarCondicion(CobroAdministrativo $obj, DeleteCondicionRequest $request) {
		$this->objEntidad($obj, "No tiene autorización para editar el cobro administrativo");
		$condicion = CondicionCobroAdministrativoRango::find($request->condicion);
		$condicion->delete();
		return response()->json(["ok" => true], 200);
	}

	public function modalidades(CobroAdministrativo $obj) {
		$this->objEntidad($obj, "No tiene autorización para editar el cobro administrativo");
		$this->log("Ingresó a asociar modalidades a cobro administrativo [$obj->id]");
		$modalidades = Modalidad::entidadId()->orderBy('nombre')->get();
		return view('creditos.cobroAdministrativo.modalidades')->withCobro($obj)->withModalidades($modalidades);
	}

	public function asociar(CobroAdministrativo $obj, Modalidad $modalidad) {
		$this->objEntidad($obj, 'No tiene autorización para editar el cobro administrativo');
		$this->objEntidad($modalidad, 'No tiene autorización para editar el cobro administrativo');
		if($modalidad->cobrosAdministrativos->count()) {
			if($modalidad->cobrosAdministrativos[0]->id != $obj->id) {
				$modalidad->cobrosAdministrativos()->toggle($modalidad->cobrosAdministrativos[0]);
				$estado = $obj->modalidades->count() == 1 ? true : false;
				return response()->json(["asociado" => $estado]);
			}
		}
		$obj->modalidades()->toggle($modalidad);
		$estado = $modalidad->cobrosAdministrativos->count() == 1 ? false : true;
		return response()->json(["asociado" => $estado]);
	}

	public static function routes() {
		Route::get('cobrosAdministrativos', 'Creditos\CobroAdministrativoController@index');
		Route::get('cobrosAdministrativos/create', 'Creditos\CobroAdministrativoController@create');
		Route::post('cobrosAdministrativos', 'Creditos\CobroAdministrativoController@store');
		Route::get('cobrosAdministrativos/{obj}/edit', 'Creditos\CobroAdministrativoController@edit')->name('cobrosAdministrativos.edit');
		Route::put('cobrosAdministrativos/{obj}', 'Creditos\CobroAdministrativoController@update');
		Route::post('cobrosAdministrativos/{obj}', 'Creditos\CobroAdministrativoController@guardarCondicion');
		Route::delete('cobrosAdministrativos/{obj}', 'Creditos\CobroAdministrativoController@eliminarCondicion');
		Route::get('cobrosAdministrativos/{obj}/modalidades', 'Creditos\CobroAdministrativoController@modalidades')->name('cobrosAdministrativos.modalidades');
		Route::get('cobrosAdministrativos/{obj}/{modalidad}', 'Creditos\CobroAdministrativoController@asociar');
	}
}
