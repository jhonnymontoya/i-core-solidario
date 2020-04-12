<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\CuentaAhorros\CreateCuentaAhorrosRequest;
use App\Http\Requests\Ahorros\CuentaAhorros\EditCuentaAhorrosRequest;
use App\Models\Ahorros\CuentaAhorro;
use App\Models\Ahorros\TipoCuentaAhorro;
use App\Models\General\Tercero;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CuentaAhorrosController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a cuentas de ahorros", $request);
		$entidad = $this->getEntidad();
		$request = $request->validate([
			'name'			=> 'bail|nullable|string|max:50',
			'tipoCuenta'	=> [
								'bail',
								'nullable',
								'integer',
								'exists:sqlsrv.ahorros.tipos_cuentas_ahorros,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
							],
			'estado'		=> 'bail|nullable|string|in:APERTURA,ACTIVA,INACTIVA,CERRADA'
		]);
		$tiposCuentaAhorros = TipoCuentaAhorro::entidadId()->pluck('nombre_producto', 'id');
		$cuentasAhorros = CuentaAhorro::with('socioTitular', 'tipoCuentaAhorro', 'tarjetahabientes')->entidadId()->orderBy('titular_socio_id', 'asc')->orderBy('nombre_deposito', 'asc');
		if(isset($request["name"])) {
			$cuentasAhorros = $cuentasAhorros->search($request["name"]);
		}
		if(isset($request["tipoCuenta"])) {
			$cuentasAhorros = $cuentasAhorros->whereTipoCuentaAhorroId($request["tipoCuenta"]);
		}
		if(isset($request["estado"])) {
			$cuentasAhorros = $cuentasAhorros->estado($request["estado"]);
		}
		$cuentasAhorros = $cuentasAhorros->paginate();
		return view("ahorros.cuentaAhorros.index")->withCuentasAhorros($cuentasAhorros)->withTiposCuentaAhorros($tiposCuentaAhorros);
	}

	public function create() {
		$this->log("Ingresó a crear cuentas de ahorros");
		$tiposCuentaAhorros = TipoCuentaAhorro::entidadId()->estaActiva()->pluck('nombre_producto', 'id');
		return view("ahorros.cuentaAhorros.create")->withTiposCuentaAhorros($tiposCuentaAhorros);
	}

	public function store(CreateCuentaAhorrosRequest $request) {
		$this->log("Creó cuenta de ahorros con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$entidad = $this->getEntidad();
		$cuentaAhorros = new CuentaAhorro;
		$cuentaAhorros->fill($request->all());
		$cuentaAhorros->entidad_id = $entidad->id;
		$cuentaAhorros->numero_cuenta = CuentaAhorro::obtenerSiguienteNumeroCuentaAhorros($entidad->id);
		$cuentaAhorros->nombre_deposito = Str::limit($cuentaAhorros->numero_cuenta . '-' . $cuentaAhorros->socioTitular->tercero->nombre, 50);
		$cuentaAhorros->cupo_flexible = 0;
		$cuentaAhorros->estado = 'ACTIVA';
		$cuentaAhorros->fecha_apertura = Carbon::now()->startOfDay();

		$cuentaAhorros->save();
		Session::flash('message', 'Se ha creado la cuenta de ahorros \'' . $cuentaAhorros->numero_cuenta . '\'');
		return redirect()->route('cuentaAhorros.edit', $cuentaAhorros->id);
	}

	public function edit(CuentaAhorro $obj) {
		$this->objEntidad($obj, 'No autorizado a editar la cuenta de ahorro');
		$this->log("Ingresó a editar el cuenta de ahorros " . $obj->id);
		$tiposCuentaAhorros = TipoCuentaAhorro::entidadId()->estaActiva()->pluck('nombre_producto', 'id');
		return view("ahorros.cuentaAhorros.edit")->withCuentaAhorro($obj)->withTiposCuentaAhorros($tiposCuentaAhorros);
	}

	public function update(CuentaAhorro $obj, EditCuentaAhorrosRequest $request) {
		$this->objEntidad($obj, 'No autorizado a editar la cuenta de ahorro');
		$mensaje = sprintf("Actualizó la cuenta de ahorros '%s' con los siguientes parámetros " . json_encode($request->all()), $obj->id);
		$this->log($mensaje, "ACTUALIZAR");
		$obj->nombre_deposito = $request->nombre_deposito;
		$obj->cupo_flexible = $request->cupo_flexible;
		$obj->save();
		Session::flash('message', 'Se ha actualizado la cuenta de ahorros \'' . $obj->numero_cuenta . '\'');
		return redirect('cuentaAhorros');
	}

	public function getCuentasPorTercero(Tercero $tercero) {
		$this->objEntidad($tercero, "No autorizado");
		$items = array();
		if(!empty($tercero->socio)) {
			$cuentas = $tercero->socio->cuentasAhorros()->orderBy('numero_cuenta')->get();
			foreach($cuentas as $cuenta) {
				$item = array(
					'id' => $cuenta->id,
					'text' => $cuenta->numero_cuenta,
					'numeroCuenta' => $cuenta->numero_cuenta,
					'nombreDeposito' => $cuenta->nombre_deposito,
					'estado' => $cuenta->estado,
					'cupoFlexible' => $cuenta->cupo_flexible,
					'cuentaVinculada' => $cuenta->tarjetahabientes->count() ? true : false,
					'fechaApertura' => $cuenta->fecha_apertura,
				);
				array_push($items, $item);
			}
		}

		$resultado = array('total_count' => count($items), 'incomplete_results' => false);
		$resultado['items'] = $items;
		return response()->json($resultado);
	}

	public static function routes() {
		Route::get('cuentaAhorros', 'Ahorros\CuentaAhorrosController@index');
		Route::get('cuentaAhorros/create', 'Ahorros\CuentaAhorrosController@create');
		Route::post('cuentaAhorros', 'Ahorros\CuentaAhorrosController@store');
		Route::get('cuentaAhorros/{obj}/edit', 'Ahorros\CuentaAhorrosController@edit')->name('cuentaAhorros.edit');
		Route::put('cuentaAhorros/{obj}', 'Ahorros\CuentaAhorrosController@update');

		Route::get('cuentaAhorros/{tercero}/cuentas', 'Ahorros\CuentaAhorrosController@getCuentasPorTercero');
	}
}
