<?php

namespace App\Http\Controllers\Socios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Socio\CuotaObligatoria\CreateCuotaObligatoriaRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\General\Tercero;
use App\Models\Socios\CuotaObligatoria;
use App\Models\Socios\Socio;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class CuotaObligatoriaController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$socio = Socio::with('cuotasObligatorias')->estado('ACTIVO')->whereId($request->socio)->first();
		return view('socios.cuotaObligatoria.index')->withSocio($socio);
	}

	public function create(Socio $obj) {
		$tiposCuotas = ModalidadAhorro::entidadId()->obligatorio()->activa(true)->get();
		return view('socios.cuotaObligatoria.create')->withSocio($obj)->withTiposCuotasObligatorias($tiposCuotas);
	}

	public function update(CreateCuotaObligatoriaRequest $request, Socio $obj) {
		foreach ($request->tipoCuota as $id => $valor) {
			$cuota = CuotaObligatoria::entidadId()->socioId($obj->id)->modalidadAhorroId($valor)->first();
			if(!$cuota)$cuota = new CuotaObligatoria;
			$cuota->entidad_id				= $this->getEntidad()->id;
			$cuota->socio_id				= $obj->id;
			$cuota->modalidad_ahorro_id		= $valor;
			$cuota->tipo_calculo			= $request->factor[$id];
			$cuota->valor					= $request->valor[$id];
			$cuota->save();
		}
		Session::flash('message', 'Se ha actualizado las cuotas obligatorias');
		return redirect('cuotaObligatoria?socio=' . $obj->id);
	}

	public static function routes() {
		Route::get('cuotaObligatoria', 'Socios\CuotaObligatoriaController@index');
		Route::get('cuotaObligatoria/{obj}', 'Socios\CuotaObligatoriaController@create')->name('cuotaObligatoriaCreate');
		Route::put('cuotaObligatoria/{obj}', 'Socios\CuotaObligatoriaController@update');
	}
}
