<?php

namespace App\Http\Controllers\Recaudos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recaudos\ConceptosRecaudos\CreateConceptoRecaudoRequest;
use App\Http\Requests\Recaudos\ConceptosRecaudos\EditConceptoRecaudoRequest;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\Modalidad;
use App\Models\Recaudos\ConceptoRecaudos;
use App\Models\Recaudos\Pagaduria;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class ConceptosRecaudosController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$entidadId = $this->getEntidad()->id;
		$req = $request->validate([
			'name' => 'bail|nullable|string|max:50',
			'pagaduria' => [
					'bail',
					'nullable',
					'exists:sqlsrv.recaudos.pagadurias,id,entidad_id,' . $entidadId . ',deleted_at,NULL'
			]
		]);
		$conceptosRecaudos = ConceptoRecaudos::whereHas('pagaduria', function($q) use($entidadId, $req){
			if(isset($req["pagaduria"]))
				$q->entidadId($entidadId)->whereId($req["pagaduria"]);
			else
				$q->entidadId($entidadId);
		})
		->orderBy('pagaduria_id')
		->orderBy('codigo');

		if(isset($req["name"]))$conceptosRecaudos->search($req["name"]);
		$conceptosRecaudos = $conceptosRecaudos->paginate();

		$pagadurias = Pagaduria::entidadId()->orderBy("nombre")->pluck('nombre', 'id');
		return view('recaudos.conceptosRecaudos.index')
			->withConceptosRecaudos($conceptosRecaudos)
			->withPagadurias($pagadurias);
	}

	public function create() {
		$pagadurias = Pagaduria::entidadId()->pluck('nombre', 'id');
		return view('recaudos.conceptosRecaudos.create')->withPagadurias($pagadurias);
	}

	public function store(CreateConceptoRecaudoRequest $request) {
		$conceptoRecaudo = new ConceptoRecaudos;
		$conceptoRecaudo->fill($request->all());
		$conceptoRecaudo->save();
		Session::flash('message', 'Se ha creado el concepto de recaudo \'' . $conceptoRecaudo->nombre . '\'');
		return redirect()->route('conceptosRecaudosEdit', $conceptoRecaudo->id);
	}

	public function edit(ConceptoRecaudos $obj) {
		$pagadurias = Pagaduria::entidadId()->pluck('nombre', 'id');
		$modalidadesAhorros = ModalidadAhorro::entidadId()->with('conceptosRecaudos')->get();
		$modalidadesCreditos = Modalidad::entidadId()->with('conceptosRecaudos')->get();
		return view('recaudos.conceptosRecaudos.edit')
					->withConceptoRecaudo($obj)
					->withPagadurias($pagadurias)
					->withModalidadesAhorros($modalidadesAhorros)
					->withModalidadesCreditos($modalidadesCreditos);
	}

	public function update(EditConceptoRecaudoRequest $request, ConceptoRecaudos $obj) {
		$obj->codigo = $request->codigo;
		$obj->nombre = $request->nombre;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el concepto de recaudo \'' . $obj->nombre . '\'');
		return redirect('conceptosRecaudos');
	}

	public function asociarModalidadAhorro(Request $request, ConceptoRecaudos $obj) {
		Validator::make($request->all(),
			[
				'modalidadAhorroId'		=> [
											'bail',
											'required',
											'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL',
										],
			], [], []
		)->validate();

		$modalidad = ModalidadAhorro::find($request->modalidadAhorroId);
		$conceptosEnPagaduria = $obj->pagaduria->conceptosRecaudos->pluck('id');
		$modalidad->conceptosRecaudos()->detach($conceptosEnPagaduria->toArray());
		$modalidad->conceptosRecaudos()->attach($obj);
		return response()->json(["concepto" => $obj->codigo . ' - ' . $obj->nombre]);
	}

	public function asociarModalidadCredito(Request $request, ConceptoRecaudos $obj) {
		Validator::make($request->all(),
			[
				'modalidadCreditoId'		=> [
											'bail',
											'required',
											'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $this->getEntidad()->id . ',deleted_at,NULL',
										],
			], [], []
		)->validate();
		$modalidad = Modalidad::find($request->modalidadCreditoId);
		$conceptosEnPagaduria = $obj->pagaduria->conceptosRecaudos->pluck('id');
		$modalidad->conceptosRecaudos()->detach($conceptosEnPagaduria->toArray());
		$modalidad->conceptosRecaudos()->attach($obj);
		return response()->json(["concepto" => $obj->codigo . ' - ' . $obj->nombre]);
	}

	public static function routes() {
		Route::get('conceptosRecaudos', 'Recaudos\ConceptosRecaudosController@index');
		Route::get('conceptosRecaudos/create', 'Recaudos\ConceptosRecaudosController@create');
		Route::post('conceptosRecaudos', 'Recaudos\ConceptosRecaudosController@store');
		Route::get('conceptosRecaudos/{obj}/edit', 'Recaudos\ConceptosRecaudosController@edit')->name('conceptosRecaudosEdit');
		Route::put('conceptosRecaudos/{obj}', 'Recaudos\ConceptosRecaudosController@update');
		
		Route::post('conceptosRecaudos/{obj}/asociarModalidadAhorro', 'Recaudos\ConceptosRecaudosController@asociarModalidadAhorro')->name('conceptosRecaudosAdociarAhorro');
		Route::post('conceptosRecaudos/{obj}/asociarModalidadCredito', 'Recaudos\ConceptosRecaudosController@asociarModalidadCredito')->name('conceptosRecaudosAdociarCredito');
	}
}
