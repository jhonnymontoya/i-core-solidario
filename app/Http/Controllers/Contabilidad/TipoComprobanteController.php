<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contabilidad\TipoComprobante\CreateTipoComprobanteRequest;
use App\Http\Requests\Contabilidad\TipoComprobante\EditTipoComprobanteRequest;
use App\Models\Contabilidad\Modulo;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\Entidad;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class TipoComprobanteController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin')->except(['getTipoComprobante']);
		$this->middleware('verEnt')->except(['getTipoComprobante']);
		$this->middleware('verMenu')->except(['getTipoComprobante']);
	}

	public function index(Request $request) {
		$tiposComprobantes = TipoComprobante::search($request->name)
										->entidadId($this->getEntidad()->id)
										->orderBy('uso')
										->orderBy('nombre')
										->paginate();
		return view('contabilidad.tipoComprobante.index')->withTiposComprobantes($tiposComprobantes);
	}

	public function create() {
		$formatos = array(
					'COMPROBANTECONTABLE' => 'Comprobante contable',
					'NOTACREDITO' => 'Nota crédito',
					'NOTADEBITO' => 'Nota débito',
					'PAGOIMPUESTOS' => 'Pago impuestos',
					'RECIBOCAJA' => 'Recibo caja',
			);
		$comprobantes = array(
					'INGRESO' => 'Ingreso',
					'EGRESO' => 'Egreso',
					'NOTACONTABLE' => 'Nota contable',
			);

		$modulos = Modulo::activo(true)->orderBy('nombre')->pluck('nombre', 'id');

		return view('contabilidad.tipoComprobante.create')
						->withFormatos($formatos)
						->withComprobantes($comprobantes)
						->withModulos($modulos);
	}

	public function store(CreateTipoComprobanteRequest $request) {
		$entidad = $this->getEntidad();
		$tipoComprobante = $entidad->tiposComprobantes()->create($request->all());
		Session::flash('message', 'Se ha guardado el tipo de comprobante \'' . $tipoComprobante->nombre . '\'');
		return redirect('tipoComprobante');


	}

	public function edit(TipoComprobante $obj) {
		if($obj->es_uso_proceso) {
			Session::flash('error', 'No se puede editar este tipo de comprobante.');
			return redirect('tipoComprobante');
		}
		$formatos = array(
					'COMPROBANTECONTABLE' => 'Comprobante contable',
					'NOTACREDITO' => 'Nota crédito',
					'NOTADEBITO' => 'Nota débito',
					'PAGOIMPUESTOS' => 'Pago impuestos',
					'RECIBOCAJA' => 'Recibo caja',
			);
		$comprobantes = array(
					'INGRESO' => 'Ingreso',
					'EGRESO' => 'Egreso',
					'NOTACONTABLE' => 'Nota contable',
			);
		$modulos = Modulo::activo(true)->orderBy('nombre')->pluck('nombre', 'id');

		return view('contabilidad.tipoComprobante.edit')
						->withFormatos($formatos)
						->withComprobantes($comprobantes)
						->withTipoComprobante($obj)
						->withModulos($modulos);
	}

	public function update(TipoComprobante $obj, EditTipoComprobanteRequest $request) {
		if($obj->es_uso_proceso) {
			Session::flash('error', 'No se puede editar este tipo de comprobante.');
			return redirect('tipoComprobante');
		}
		$obj->fill($request->all());
		$obj->save();
		Session::flash('message', 'Se ha actualizado el tipo de comprobante \'' . $obj->nombre . '\'');
		return redirect('tipoComprobante');
	}

	////////////////////////////API

	public function getTipoComprobante(Request $request) {
		if(!empty($request->q)) {
			$tiposComprobantes = TipoComprobante::entidadId($request->entidad)
							->search($request->q)
							->paraComprobante()
							->entidadId($request->entidad)
							->uso('MANUAL')
							->limit(20)
							->get();
		}
		elseif(!empty($request->id)) {
			$tiposComprobantes = TipoComprobante::paraComprobante()->whereId($request->id)->uso('MANUAL')->take(1)->get();
		}
		else {
			$tiposComprobantes = TipoComprobante::entidadId($request->entidad)
							->paraComprobante()
							->entidadId($request->entidad)
							->uso('MANUAL')
							->limit(20)
							->get();
		}

		$resultado = array('total_count' => $tiposComprobantes->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($tiposComprobantes as $cuenta) {
			$item = array('id' => $cuenta->id, 'text' => $cuenta->nombre_completo);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	public static function routes() {
		Route::get('tipoComprobante', 'Contabilidad\TipoComprobanteController@index');
		Route::get('tipoComprobante/create', 'Contabilidad\TipoComprobanteController@create');
		Route::post('tipoComprobante', 'Contabilidad\TipoComprobanteController@store');
		Route::get('tipoComprobante/{obj}/edit', 'Contabilidad\TipoComprobanteController@edit')->name('tipoComprobanteEdit');
		Route::put('tipoComprobante/{obj}', 'Contabilidad\TipoComprobanteController@update');
	}
}
