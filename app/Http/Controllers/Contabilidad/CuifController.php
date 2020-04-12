<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contabilidad\Cuif\CreateCuifRequest;
use App\Http\Requests\Contabilidad\Cuif\EditCuifRequest;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\Modulo;
use App\Models\General\Entidad;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class CuifController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$cuentas = Cuif::entidadId()
						->nombre($request->get("name"))
						->tipoCuenta($request->get("tipo"))
						->nivel($request->get("nivel"))
						->moduloId($request->get("modulo"))
						->activa($request->get("estado"))
						->orderBy('codigo', 'asc')
						->paginate(25);

		$modulos = Modulo::activo(true)->orderBy('nombre')->pluck('nombre', 'id');

		return view('contabilidad.cuenta.index')->withCuentas($cuentas)->withModulos($modulos);
	}

	public function create() {
		$modulos = Modulo::orderBy('nombre')->pluck('nombre', 'id')->all();
		return view('contabilidad.cuenta.create')->withModulos($modulos);
	}

	public function store(CreateCuifRequest $request) {
		$nivel = $this->nivel($request->get('codigo'));

		if($nivel <= 4){
			$this->validate($request, ['modulo' => 'nullable|regex:/^[1]$/'], ['regex' => 'No se puede asignar un módulo a una cuenta no Auxiliar']);
		}
		else{
			$this->validate($request, ['modulo' => 'required|integer|min:1'], ['min' => 'Se debe asignar un módulo si la cuenta es Auxiliar']);
		}

		switch ($request->get('modulo')) {
			case 3:case 6:{
				$this->validate($request,
					[
						'negativo'		=> 'required|integer|min:0|max:0',
						'resultado'		=> 'required|integer|min:0|max:0',
						'ordent'		=> 'required|integer|min:0|max:0',
					],
					[
						'max'			=> 'Para la aplicación dada, :attribute debe ser No',
					]
				);
				break;
			}
			case 7:{
				$this->validate($request,
					[
						'negativo'		=> 'required|boolean',
						'resultado'		=> 'required|integer|min:0|max:0',
						'ordent'		=> 'required|integer|min:0|max:0',
					],
					[
						'max'			=> 'Para la aplicación dada, :attribute debe ser No',
					]
				);
				break;
			}
			case 1:case 4:case 5:{
				$this->validate($request,
					[
						'resultado'		=> 'required|integer|min:0|max:0',
						'ordent'		=> 'required|integer|min:0|max:0',
					],
					[
						'max'			=> 'Para la aplicación dada :attribute debe ser No',
					]
				);
				break;
			}
		}

		if($request->ordent == 1) {
			$this->validate($request, ['orden' => 'required'], ['required' => 'Se necesita una cuenta de compensación']);
			if($this->nivel($request->orden) <= 4) {
				$this->validate($request, ['orden' => 'max:0'], ['max' => 'La cuenta de compensación debe ser auxiliar']);
			}
			if($this->categoria($request->orden) != 'ORDEN') {
				$this->validate($request, ['orden' => 'max:0'], ['max' => 'La cuenta de compensación debe ser de la categoría ORDEN']);
			}
		}

		$cuenta = new Cuif;

		$cuenta->codigo					= $request->codigo;
		$cuenta->nombre					= $nivel<=4?mb_strtoupper($request->nombre):ucwords(mb_strtolower($request->nombre));
		$cuenta->nivel					= $nivel;
		$cuenta->tipo_cuenta			= $this->tipoCuenta($nivel);
		$cuenta->categoria				= $this->categoria($request->codigo);
		$cuenta->naturaleza				= $request->naturaleza;
		$cuenta->acepta_saldo_negativo	= $request->negativo;
		$cuenta->es_pyg					= $request->resultado;
		$cuenta->cuenta_orden			= empty($request->orden) ? null : $request->orden;
		$cuenta->comentario				= empty($request->comentario) ? null : $request->comentario;
		if($request->modulo) {
			$cuenta->modulo_id				= $request->modulo;
		}

		$cuenta->entidad_id = $this->getEntidad()->id;

		if($cuenta->cuenta_padre == null) {
			return redirect()
						->back()
						->withErrors(['codigo' => 'No existe cuenta padre para la cuenta \'' . $cuenta->codigo . '\''])
						->withInput();
		}
		$cuenta->save();

		Session::flash('message', 'Se ha creado la cuenta \'' . $cuenta->full . '\'');

		return redirect('cuentaContable');
	}

	public function edit(Cuif $obj) {
		$modulos = Modulo::orderBy('nombre')->pluck('nombre', 'id')->all();
		return view('contabilidad.cuenta.edit')->withModulos($modulos)->withCuenta($obj);
	}

	public function update(EditCuifRequest $request, Cuif $obj) {
		$obj->nombre				= $obj->nivel <= 4 ? mb_strtoupper($request->nombre) : ucwords(mb_strtolower($request->nombre));
		$obj->naturaleza			= $request->naturaleza;
		$obj->acepta_saldo_negativo	= $request->negativo;
		$obj->comentario			= empty($request->comentario) ? null : $request->comentario;
		$obj->esta_activo			= $request->esta_activo;
		$obj->es_pyg				= $request->resultado;

		$obj->save();
		Session::flash('message', 'Se ha actualizado la cuenta \'' . $obj->full . '\'');
		return redirect('cuentaContable');
	}

	/**
	 * Devuelve el nivel de la cuenta
	 *
	 * @return Int
	 */
	private function nivel($codigo) {
		$len = strlen($codigo);
		switch ($len) {
			case 1:{$nivel = 1;break;}
			case 2:{$nivel = 2;break;}
			case 3:case 4:{$nivel = 3;break;}
			case 5:case 6:{$nivel = 4;break;}
			case 7:case 8:{$nivel = 5;break;}
			case 9:case 10:{$nivel = 6;break;}
			case 11:case 12:{$nivel = 7;break;}
			case 13:case 14:{$nivel = 8;break;}
			case 15:case 16:{$nivel = 9;break;}
			case 17:case 18:{$nivel = 10;break;}
		}

		return $nivel;
	}

	private function categoria($codigo) {
		$digito = $codigo[0];
		$categoria = "";

		switch($digito){
			case 0:{$categoria = "";break;}
			case 1:case 2:case 3:{$categoria = "BALANCE GENERAL";break;}
			case 4:case 5:case 6:case 7:{$categoria = "ESTADO DE GANANCIA O PÉRDIDAS";break;}
			case 8:case 9:{$categoria = "ORDEN";break;}
			default:{$categoria = "";break;}
		}
		return $categoria;
	}

	private function tipoCuenta($nivel) {
		$tipoCuenta = "";
		switch ($nivel){
			case 1:{$tipoCuenta = "CLASE";break;}
			case 2:{$tipoCuenta = "GRUPO";break;}
			case 3:{$tipoCuenta = "CUENTA";break;}
			case 4:{$tipoCuenta = "SUBCUENTA";break;}
			default:{$tipoCuenta = "AUXILIAR";break;}
		}

		return $tipoCuenta;
	}

	public function cuentaPadre(Request $request) {
		if(strlen($request->codigo) < 1 || strlen($request->codigo) > 18)return response()->json([]);

		$validator = Validator::make($request->all(), [
			'codigo' => [
							'bail',
							'required',
							'regex:/^([1-9]([1-9](([0-9][1-9])|([1-9][0-9])){0,8})?)?$/',
						],
		]);

		if($validator->fails())return response()->json([]);

		$nivel = $this->nivel($request->codigo);
		$cuenta = new Cuif(['nivel' => $nivel, 'codigo' => $request->codigo, 'entidad_id' => $this->getEntidad()->id]);
		$cuenta = $cuenta->cuenta_padre;

		return response()->json($cuenta);
	}

	public function getCuenta(Request $request) {
		if(!empty($request->q)) {
			$cuentas = Cuif::entidadId($this->getEntidad()->id)
							->activa()
							->TipoCuenta('AUXILIAR')
							->paraComprobante()
							->nombre($request->q)
							->limit(20)
							->get();
		}
		elseif(!empty($request->id)) {
			//$cuentas = Tercero::activo()->whereId($request->id)->take(1)->get();
		}
		else {
			$cuentas = Cuif::entidadId()->activa()->TipoCuenta('AUXILIAR')->paraComprobante()->limit(20)->get();
		}

		$resultado = array('total_count' => $cuentas->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($cuentas as $cuenta) {
			$item = array('id' => $cuenta->id, 'text' => $cuenta->codigo . ' - ' . $cuenta->nombre);
			array_push($resultado['items'], $item);
		}

		return response()->json($resultado);
	}

	public function getCuentaAuxiliarAhorros(Request $request) {
		if(!empty($request->q)) {
			$cuentas = Cuif::entidadId()->activa()->TipoCuenta('AUXILIAR')->whereModuloId(6)->nombre($request->q)->limit(20)->get();
		}
		elseif(!empty($request->id)) {
			$cuentas = Cuif::entidadId()->activa()->TipoCuenta('AUXILIAR')->whereModuloId(6)->whereId($request->id)->take(1)->get();
		}
		else {
			$cuentas = Cuif::entidadId()->activa()->TipoCuenta('AUXILIAR')->whereModuloId(6)->limit(20)->get();
		}

		$resultado = array('total_count' => $cuentas->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($cuentas as $cuenta){
			$item = array('id' => $cuenta->id, 'text' => $cuenta->codigo . ' - ' . $cuenta->nombre);
			array_push($resultado['items'], $item);
		}

		return response()->json($resultado);
	}

	public function getCuentaConParametros(Request $request) {
		Validator::make($request->all(), [
			'id'						=> 'exists:sqlsrv.contabilidad.cuifs,id,deleted_at,NULL',
			'q'							=> 'string|max:100',
			'tipoCuenta'				=> 'string|in:CLASE,GRUPO,CUENTA,SUBCUENTA,AUXILIAR',
			'categoria'					=> 'string|in:ESTADO DE GANANCIA O PÉRDIDAS,BALANCE GENERAL,ORDEN',
			'naturaleza'				=> 'string|in:DÉBITO,CRÉDITO',
			'estado'					=> 'boolean',
			'aceptaSaldoNegativo'		=> 'boolean',
			'pyg'						=> 'boolean',
			'modulo'					=> 'regex:/^([1-9])(,[1-9])*$/'
		])->validate();

		$cuentas = Cuif::entidadId($this->getEntidad()->id)->limit(20);

		//filtro por tipo de cuenta
		if($request->has('tipoCuenta')){
			$cuentas->whereTipoCuenta($request->tipoCuenta);
		}

		//filtro por categoría
		if($request->has('categoria')){
			$cuentas->whereCategoria($request->categoria);
		}

		//filtro por naturaleza
		if($request->has('naturaleza')){
			$cuentas->whereNaturaleza($request->naturaleza);
		}

		//filtro por estado
		if($request->has('estado')){
			$cuentas->whereEstaActivo($request->estado);
		}

		//filtro por acepta saldo negativo
		if($request->has('aceptaSaldoNegativo')){
			$cuentas->whereAceptaSaldoNegativo($request->aceptaSaldoNegativo);
		}

		//filtro de es pyg
		if($request->has('pyg')){
			$cuentas->whereEsPyg($request->pyg);
		}

		//filtro de nombre
		if($request->has('q')){
			$cuentas->nombre($request->q);
		}

		//filtro de modulos
		if($request->has('modulo')) {
			$cuentas->whereIn('modulo_id', explode(',', $request->modulo));
		}

		//filtro de id
		if($request->has('id')){
			$cuentas->whereId($request->id);
		}

		$cuentas = $cuentas->get();

		$resultado = array('total_count' => $cuentas->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach($cuentas as $cuenta){
			$item = array('id' => $cuenta->id, 'text' => $cuenta->codigo . ' - ' . $cuenta->nombre);
			array_push($resultado['items'], $item);
		}

		return response()->json($resultado);
	}

	public static function routes(){
		Route::get('cuentaContable', 'Contabilidad\CuifController@index');
		Route::get('cuentaContable/create', 'Contabilidad\CuifController@create');
		Route::post('cuentaContable', 'Contabilidad\CuifController@store');
		Route::get('cuentaContable/{obj}/edit', 'Contabilidad\CuifController@edit')->name('cuentaEdit');
		Route::put('cuentaContable/{obj}', 'Contabilidad\CuifController@update');

		Route::get('cuentaContable/padre', 'Contabilidad\CuifController@cuentaPadre');
		Route::get('cuentaContable/cuentaContable', 'Contabilidad\CuifController@getCuenta');
		Route::get('cuentaContable/cuentaContableAuxiliarAhorros', 'Contabilidad\CuifController@getCuentaAuxiliarAhorros');

		Route::get('cuentaContable/getCuentaConParametros', 'Contabilidad\CuifController@getCuentaConParametros');
	}
}
