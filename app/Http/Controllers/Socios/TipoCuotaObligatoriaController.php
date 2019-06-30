<?php

namespace App\Http\Controllers\Socios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Socio\TipoCuotaObligatoria\CreateTipoCuotaObligatoriaRequest;
use App\Http\Requests\Socio\TipoCuotaObligatoria\EditTipoCuotaObligatoriaRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class TipoCuotaObligatoriaController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$cuotasObligatorias = ModalidadAhorro::entidadId()->obligatorio()->activa($request->estado)->search($request->name)->paginate();
		return view('socios.tipoCuotaObligatoria.index')->withCuotas($cuotasObligatorias);
	}

	public function create() {
		return view('socios.tipoCuotaObligatoria.create');
	}

	public function store(CreateTipoCuotaObligatoriaRequest $request) {
		$cuota = new ModalidadAhorro;
		$cuota->entidad_id = $this->getEntidad()->id;
		$cuota->fill($request->all());
		$cuota->tipo_ahorro = 'OBLIGATORIO';
		$cuota->valor_tope = empty($request->tope) ? null : $request->tope;
		$cuota->save();
		Session::flash('message', 'Se ha creado la cuota obligatoria \'' . $cuota->nombre . '\'');
		return redirect('tipoCuotaObligatoria');
	}

	public function edit(ModalidadAhorro $obj) {
		return view('socios.tipoCuotaObligatoria.edit')->withCuota($obj);
	}

	public function update(EditTipoCuotaObligatoriaRequest $request, ModalidadAhorro $obj) {
		$obj->tipo_calculo	= $request->tipo_calculo;
		$obj->valor			= $request->valor;
		$obj->valor_tope	= empty($request->tope) ? null : $request->tope;
		$obj->esta_activa	= $request->esta_activa;
		$obj->save();
		Session::flash('message', 'Se ha actualizado la cuota obligatoria \'' . $obj->nombre . '\'');
		return redirect('tipoCuotaObligatoria');
	}

	public static function routes() {
		Route::get('tipoCuotaObligatoria', 'Socios\TipoCuotaObligatoriaController@index');
		Route::get('tipoCuotaObligatoria/create', 'Socios\TipoCuotaObligatoriaController@create');
		Route::post('tipoCuotaObligatoria', 'Socios\TipoCuotaObligatoriaController@store');
		Route::get('tipoCuotaObligatoria/{obj}/edit', 'Socios\TipoCuotaObligatoriaController@edit')->name('tipoCuotaObligatoriaEdit');
		Route::put('tipoCuotaObligatoria/{obj}', 'Socios\TipoCuotaObligatoriaController@update');
	}
}
