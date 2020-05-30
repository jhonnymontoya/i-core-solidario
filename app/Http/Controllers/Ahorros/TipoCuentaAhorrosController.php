<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\TipoCuentaAhorros\CreateTipoCuentaAhorrosRequest;
use App\Http\Requests\Ahorros\TipoCuentaAhorros\EditTipoCuentaAhorrosRequest;
use App\Models\Ahorros\TipoCuentaAhorro;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class TipoCuentaAhorrosController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a tipos de cuentas de ahorros", $request);
		$request = $request->validate([
			'name'			=> 'bail|nullable|string|max:50',
			'estado'		=> 'bail|nullable|boolean'
		]);
		$tiposCuentasAhorros = TipoCuentaAhorro::with('cuentasAhorros', 'capitalCuif')->entidadId()->orderBy('nombre_producto', 'asc');
		if(isset($request["name"])) {
			$tiposCuentasAhorros = $tiposCuentasAhorros->search($request["name"]);
		}
		if(isset($request["estado"])) {
			$tiposCuentasAhorros = $tiposCuentasAhorros->estaActiva($request["estado"]);
		}
		$tiposCuentasAhorros = $tiposCuentasAhorros->paginate();
		return view("ahorros.tipoCuentaAhorros.index")->withTiposCuentasAhorros($tiposCuentasAhorros);
	}

	public function create() {
		$this->log("Ingresó a crear tipos de cuentas de ahorros");
		return view("ahorros.tipoCuentaAhorros.create");
	}

	public function store(CreateTipoCuentaAhorrosRequest $request) {
		$this->log("Creó tipo de cuenta de ahorros con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$tipoCuentaAhorro = new TipoCuentaAhorro;
		$tipoCuentaAhorro->entidad_id = $this->getEntidad()->id;
		$tipoCuentaAhorro->fill($request->all());
		$tipoCuentaAhorro->save();
		Session::flash('message', 'Se ha creado el tipo de cuenta de ahorros \'' . $tipoCuentaAhorro->nombre_producto . '\'');
		return redirect('tipoCuentaAhorros');
	}

	public function edit(TipoCuentaAhorro $obj) {
		$this->objEntidad($obj, 'No autorizado a editar el tipo de cuenta de ahorro');
		$this->log("Ingresó a editar el tipo de cuenta de ahorros " . $obj->id);
		$cantidadCuentasDeAhorros = $obj->cuentasAhorros()->count();
		return view("ahorros.tipoCuentaAhorros.edit")->withTipoCuentaAhorro($obj)->withCantidadCuentasDeAhorros($cantidadCuentasDeAhorros);
	}

	public function update(TipoCuentaAhorro $obj, EditTipoCuentaAhorrosRequest $request) {
		$this->objEntidad($obj, 'No autorizado a editar el tipo de cuenta de ahorro');
		$mensaje = sprintf("Actualizó el tipo de cuentas de ahorros '%s' con los siguientes parámetros " . json_encode($request->all()), $obj->id);
		$this->log($mensaje, "ACTUALIZAR");
		$cuenta = $obj->capital_cuif_id;
		$obj->fill($request->all());
		if($obj->cuentasAhorros()->count() > 0) $obj->capital_cuif_id = $cuenta;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el tipo de cuenta de ahorros \'' . $obj->nombre_producto . '\'');
		return redirect('tipoCuentaAhorros');
	}

	public static function routes() {
		Route::get('tipoCuentaAhorros', 'Ahorros\TipoCuentaAhorrosController@index');
		Route::get('tipoCuentaAhorros/create', 'Ahorros\TipoCuentaAhorrosController@create');
		Route::post('tipoCuentaAhorros', 'Ahorros\TipoCuentaAhorrosController@store');
		Route::get('tipoCuentaAhorros/{obj}/edit', 'Ahorros\TipoCuentaAhorrosController@edit')->name('tipoCuentaAhorros.edit');
		Route::put('tipoCuentaAhorros/{obj}', 'Ahorros\TipoCuentaAhorrosController@update');
	}
}
