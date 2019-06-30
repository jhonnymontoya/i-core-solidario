<?php

namespace App\Http\Controllers\Ahorros;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\CuotaVoluntaria\CreateCuotaVoluntariaRequest;
use App\Models\Ahorros\CuotaVoluntaria;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class CuotaVoluntariaController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$socio = Socio::with('cuotasObligatorias')->estado('ACTIVO')->whereId($request->socio)->first();
		return view('ahorros.cuotaVoluntaria.index')->withSocio($socio);
	}

	public function create(Socio $obj) {
		$tiposCuotasVoluntarias = ModalidadAhorro::entidadId($this->getEntidad()->id)->voluntario()->activa(true)->get();
		$tiposCuotas = array();
		foreach ($tiposCuotasVoluntarias as $value)$tiposCuotas[$value->id] = $value->codigo . " - " . $value->nombre;

		$periodicidades = array(
				'DIARIO' => 'Diario',
				'SEMANAL' => 'Semanal',
				'DECADAL' => 'Decadal',
				'CATORCENAL' => 'Catorcenal',
				'QUINCENAL' => 'Quincenal',
				'MENSUAL' => 'Mensual',
				'BIMESTRAL' => 'Bimestral',
				'TRIMESTRAL' => 'Trimestral',
				'CUATRIMESTRAL' => 'Cuatrimestral',
				'SEMESTRAL' => 'Semestral',
				'ANUAL' => 'Anual'
			);
		$listaProgramaciones = array();
		$programaciones = $obj->pagaduria->calendarioRecaudos()->whereEstado('PROGRAMADO')->get();
		foreach($programaciones as $programacion) {
			$listaProgramaciones[$programacion->fecha_recaudo->format('d/m/Y')] = $programacion->fecha_recaudo;
		}

		return view('ahorros.cuotaVoluntaria.create')
						->withSocio($obj)
						->withTiposCuotasVoluntarias($tiposCuotas)
						->withPeriodicidades($periodicidades)
						->withProgramaciones($listaProgramaciones);
	}

	public function store(CreateCuotaVoluntariaRequest $request, Socio $obj) {
		$cuota = new CuotaVoluntaria;

		$cuota->fill($request->all());
		$cuota->socio_id = $obj->id;

		$cuota->save();

		Session::flash('message', 'Se ha agregado la cuota para \'' . $cuota->modalidadAhorro->nombre . '\'');
		return redirect('cuotaVoluntaria?socio=' . $obj->id);
	}

	public function confirmDelete(CuotaVoluntaria $obj) {
		return view('ahorros.cuotaVoluntaria.delete')->withCuota($obj);
	}

	public function delete(CuotaVoluntaria $obj) {
		$obj->delete();

		Session::flash('message', 'Se ha eliminado la cuota para \'' . $obj->modalidadAhorro->nombre . '\'');
		return redirect('cuotaVoluntaria?socio=' . $obj->socio->id);
	}

	public static function routes() {
		Route::get('cuotaVoluntaria', 'Ahorros\CuotaVoluntariaController@index');
		Route::get('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@create')->name('cuotaVoluntariaCreate');
		Route::post('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@store');
		Route::get('cuotaVoluntaria/{obj}/delete', 'Ahorros\CuotaVoluntariaController@confirmDelete')->name('cuotaVoluntariaDelete');
		Route::delete('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@delete');
	}
}
