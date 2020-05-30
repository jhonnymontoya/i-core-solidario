<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creditos\TiposGarantias\CreateTipoGarantiaRequest;
use App\Http\Requests\Creditos\TiposGarantias\EditTipoGarantiaRequest;
use App\Models\Creditos\TipoGarantia;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class TipoGarantiaController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$tiposGarantias = TipoGarantia::entidadId()->search($request->name)->paginate();
		return view('creditos.tiposGarantias.index')->withTiposGarantias($tiposGarantias);
	}

	public function create() {
		return view('creditos.tiposGarantias.create');
	}

	public function store(CreateTipoGarantiaRequest $request) {
		$tipoGarantia = new TipoGarantia;
		$tipoGarantia->fill($request->all());
		$tipoGarantia->entidad_id = $this->getEntidad()->id;
		switch ($request->condiciones) {
			case 'esPermanente':
				$tipoGarantia->es_permanente = true;
				break;
			case 'esPermanenteConDescubierto':
				$tipoGarantia->es_permanente_con_descubierto = true;
				break;
			case 'requiereGarantiaPorMonto':
				$tipoGarantia->requiere_garantia_por_monto = true;
				break;
			case 'requiereGarantiaPorValorDescubierto':
				$tipoGarantia->requiere_garantia_por_valor_descubierto = true;
				break;			
			default:
				break;
		}

		$tipoGarantia->save();
		Session::flash('message', 'Se ha creado la garantía \'' . $tipoGarantia->codigo . '\'');
		return redirect('tipoGarantia');
	}

	public function edit() {
		return redirect('tipoGarantia');
	}

	public function update(TipoGarantia $obj, EditTipoGarantiaRequest $request) {
		return redirect('tipoGarantia');
	}

	public static function routes() {
		Route::get('tipoGarantia', 'Creditos\TipoGarantiaController@index');
		Route::get('tipoGarantia/create', 'Creditos\TipoGarantiaController@create');
		Route::post('tipoGarantia', 'Creditos\TipoGarantiaController@store');
		Route::get('tipoGarantia/{obj}/edit', 'Creditos\TipoGarantiaController@edit')->name('tipoGarantiaEdit');
		Route::put('tipoGarantia/{obj}', 'Creditos\TipoGarantiaController@update');
	}
}
