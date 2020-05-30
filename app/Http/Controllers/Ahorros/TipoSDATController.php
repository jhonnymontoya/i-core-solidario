<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\TipoSDAT\CreateTipoSDATRequest;
use App\Http\Requests\Ahorros\TipoSDAT\EditTipoSDATRequest;
use App\Http\Requests\Ahorros\TipoSDAT\MakeCondicionMontoRequest;
use App\Http\Requests\Ahorros\TipoSDAT\MakeCondicionPeriodoRequest;
use App\Models\Ahorros\CondicionSDAT;
use App\Models\Ahorros\TipoSDAT;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Route;
use Session;
use Illuminate\Support\Str;

class TipoSDATController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log("Ingresó a tipos de SDAT");
		$tipoSDAT = TipoSDAT::entidadId()->paginate();
		return view('ahorros.tipoSDAT.index')->withTipos($tipoSDAT);
	}

	public function create() {
		$this->log("Ingresó a la creación de tipos SDAT");
		return view('ahorros.tipoSDAT.create');
	}

	public function store(CreateTipoSDATRequest $request) {
		$this->log("Creó tipo SDAT con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$entidad = $this->getEntidad();
		$tipo = new TipoSDAT;
		$tipo->fill($request->all());
		$tipo->entidad_id = $entidad->id;
		$tipo->save();

		$mensaje = "Se ha creado el tipo SDAT %s";
		$mensaje = sprintf($mensaje, Str::limit($tipo->nombre_completo, 50));
		Session::flash("message", $mensaje);

		return redirect('tipoSDAT');
	}

	public function edit(TipoSDAT $obj) {
		$this->objEntidad($obj);
		$msg = sprintf("Ingresó a editar el tipo SDAT '%s'", $obj->nombre_completo);
		$this->log($msg);
		$condiciones = $obj->condicionesSDAT()->orderBy('plazo_minimo')->orderBy('monto_minimo')->get();
		$datos = collect();
		foreach($condiciones as $condicion) {
			$data = [
				"plazo_minimo" => $condicion->plazo_minimo,
				"plazo_maximo" => $condicion->plazo_maximo,
				"periodo" => $condicion->periodo,
				"montos" => collect()
			];
			if(!$datos->has($condicion->periodo)) {
				$datos->put($condicion->periodo, collect($data));
			}
			if(!is_null($condicion->tasa)) {
				$data = [
					"id" => $condicion->id,
					"monto_minimo" => "$" . number_format($condicion->monto_minimo),
					"monto_maximo" => "$" . number_format($condicion->monto_maximo),
					"tasa" => number_format($condicion->tasa, 2) . "%"
				];
				$datos->get($condicion->periodo)->get("montos")->push($data);
			}
		}
		//dd($datos);
		return view('ahorros.tipoSDAT.edit')->withTipo($obj)->withCondiciones($datos);
	}

	public function update(TipoSDAT $obj, EditTipoSDATRequest $request) {
		$this->objEntidad($obj);
		$msg = "Actualizó el tipo SDAT '%s' con los siguientes parámetros %s";
		$msg = sprintf($msg, $obj->nombre_completo, json_encode($request->all()));
		$this->log($msg, "ACTUALIZAR");
		$entidad = $this->getEntidad();

		$obj->fill($request->all());
		$obj->entidad_id = $entidad->id;
		$obj->save();

		$mensaje = "Se ha actualizado el tipo SDAT %s";
		$mensaje = sprintf($mensaje, Str::limit($obj->nombre_completo, 50));
		Session::flash("message", $mensaje);

		return redirect('tipoSDAT');
	}

	public function agregarCondicionPeriodo(TipoSDAT $obj, MakeCondicionPeriodoRequest $request) {
		$msg = "Creó condición periodo para el tipo SDAT '%s' con los siguientes parámetros %s";
		$msg = sprintf($msg, $obj->nombre_completo, json_encode($request->all()));
		$this->log($msg, "CREAR");
		$entidad = $this->getEntidad();
		$condicion = new CondicionSDAT;
		$condicion->entidad_id = $entidad->id;
		$condicion->tipo_sdat_id = $obj->id;
		$condicion->plazo_minimo = $request->dd;
		$condicion->plazo_maximo = $request->dh;
		$condicion->save();

		$data = [
			"id" => $condicion->id,
			"dd" => number_format($condicion->plazo_minimo),
			"dh" => number_format($condicion->plazo_maximo)
		];
		Session::flash("message", "Se agregó con éxito el rango de tiempo");
		return response()->json($data);
	}

	public function agregarCondicionMonto(TipoSDAT $obj, MakeCondicionMontoRequest $request) {
		$msg = "Creó rango de monto para el tipo SDAT '%s' con los siguientes parámetros %s";
		$msg = sprintf($msg, $obj->nombre_completo, json_encode($request->all()));
		$this->log($msg, "CREAR");
		$entidad = $this->getEntidad();
		$condicion = $obj->condicionesSDAT()
			->wherePlazoMinimo($request->dd)
			->wherePlazoMaximo($request->dh)
			->whereNull("monto_minimo")
			->whereNull("monto_maximo")
			->first();
		if(!$condicion) {
			$condicion = New CondicionSDAT;
			$condicion->entidad_id = $entidad->id;
			$condicion->tipo_sdat_id = $obj->id;
			$condicion->plazo_minimo = $request->dd;
			$condicion->plazo_maximo = $request->dh;
		}
		$condicion->monto_minimo = $request->md;
		$condicion->monto_maximo = $request->mh;
		$condicion->tasa = $request->tasa;
		$condicion->save();

		$data = [
			"id" => $condicion->id,
			"dd" => number_format($condicion->plazo_minimo),
			"dh" => number_format($condicion->plazo_maximo),
			"md" => number_format($condicion->monto_minimo),
			"mh" => number_format($condicion->monto_maximo),
			"tasa" => number_format($condicion->tasa),
		];
		Session::flash("message", "Se agregó con éxito el rango de monto");
		return response()->json($data);
	}

	public function eliminarCondicionMonto(TipoSDAT $obj, CondicionSDAT $condicion) {
		if($condicion->tipo_sdat_id != $obj->id) {
			Session::flass("error", "No autorizado a eliminar el rango de monto");
			return redirect()->route("tipoSDAT.edit", $obj->id);
		}
		$cantidad = $obj->condicionesSDAT()
			->wherePlazoMinimo($condicion->plazo_minimo)
			->wherePlazoMaximo($condicion->plazo_maximo)
			->count();
		if($cantidad > 1) {
			$condicion->delete();
		}
		else {
			$condicion->monto_minimo = null;
			$condicion->monto_maximo = null;
			$condicion->tasa = null;
			$condicion->save();
		}
		Session::flash("message", "Se eliminó con éxito el rango de monto");
		return redirect()->route("tipoSDAT.edit", $obj->id);
	}

	public function eliminarCondicionPeriodo(TipoSDAT $obj, Request $request) {
		$condiciones = $obj->condicionesSDAT()
			->wherePlazoMinimo($request->dd)
			->wherePlazoMaximo($request->dh)
			->get();

		foreach ($condiciones as $condicion) {
			$condicion->delete();
		}


		Session::flash("message", "Se eliminó con éxito el rango de tiempo");
		return redirect()->route("tipoSDAT.edit", $obj->id);
	}

	public static function routes() {
		Route::get('tipoSDAT', 'Ahorros\TipoSDATController@index');
		Route::get('tipoSDAT/create', 'Ahorros\TipoSDATController@create');
		Route::post('tipoSDAT', 'Ahorros\TipoSDATController@store');
		Route::get('tipoSDAT/{obj}/edit', 'Ahorros\TipoSDATController@edit')->name('tipoSDAT.edit');
		Route::put('tipoSDAT/{obj}', 'Ahorros\TipoSDATController@update');

		Route::post('tipoSDAT/{obj}/condicionPeriodo', 'Ahorros\TipoSDATController@agregarCondicionPeriodo')->name('tipoSDAT.agregarCondicionPeriodo');
		Route::delete('tipoSDAT/{obj}/condicionPeriodo', 'Ahorros\TipoSDATController@eliminarCondicionPeriodo')->name('tipoSDAT.eliminarCondicionPeriodo');

		Route::put('tipoSDAT/{obj}/condicionMonto', 'Ahorros\TipoSDATController@agregarCondicionMonto')->name('tipoSDAT.agregarCondicionMonto');
		Route::delete('tipoSDAT/{obj}/{condicion}/monto', 'Ahorros\TipoSDATController@eliminarCondicionMonto')->name('tipoSDAT.eliminarCondicionMonto');
	}
}
