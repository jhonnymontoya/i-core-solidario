<?php

namespace App\Http\Controllers\Socios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Socio\RetiroSocio\CreateRetiroSocioRequest;
use App\Models\Socios\CausaRetiro;
use App\Models\Socios\Socio;
use App\Models\Socios\SocioRetiro;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;
use Log;
use Auth;

class RetiroSocioController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log(sprintf("Ingreso a retiro de socios con los siguientes parámetros %s", json_encode($request->all())), 'CONSULTAR');
		$socioRetiros = SocioRetiro::entidadId()
							->orderBy('fecha_solicitud_retiro', 'desc')
							->whereHas('socio', function($q) use($request){
								$q->search($request->name)->estado($request->estado);
							})
							->paginate();
		$estados = array('RETIRO' => 'Retiro', 'LIQUIDADO' => 'Liquidado', 'ACTIVO' => 'Activo', 'NOVEDAD' => 'Novedad');
		return view('socios.retiroSocio.index')->withSolicitudesRetiros($socioRetiros)->withEstados($estados);
	}

	public function create() {
		$this->log(sprintf("Ingreso a crear un nuevo retiro de socios"), 'INGRESAR');
		$causasRetiro = CausaRetiro::entidadId()->orderBy('nombre')->pluck('nombre', 'id');
		return view('socios.retiroSocio.create')->withCausasRetiros($causasRetiro);
	}

	public function store(CreateRetiroSocioRequest $request) {
		$this->log(sprintf("Creó un nuevo retiro de socios con los siguientes parámetros %s", json_encode($request->all())), 'CREAR');
		if($this->moduloCerrado(10, $request->fecha_solicitud_retiro)) {
			return redirect()->back()
				->withErrors(['fecha_solicitud_retiro' => 'La fecha de retiro corresponde a un periodo ya cerrado'])
				->withInput();
		}
		$retiroSocio = SocioRetiro::create($request->all());
		$retiroSocio->socio->estado = 'RETIRO';
		$retiroSocio->socio->consecutivo_retiro += 1;
		$retiroSocio->socio->fecha_ultimo_retiro = $request->fecha_solicitud_retiro;
		$retiroSocio->socio->fecha_retiro = $request->fecha_solicitud_retiro;
		$retiroSocio->socio->save();

		$usuarioWeb = $retiroSocio->socio->usuarioWeb;
		$usuarioWeb->esta_activo = false;
		$usuarioWeb->save();



		Session::flash('message', 'Se ha ingresado la solicitud de retiro para \'' . $retiroSocio->socio->tercero->nombre . '\'');
		return redirect('retiroSocio');
	}

	public function preliquidacion(Request $request) {
		$this->log(sprintf("Ingreso a la preliquidación de un socio con los siguientes parámetros %s", json_encode($request->all())), 'CONSULTAR');
		$preliquidacion = [];
		$socio = null;
		$fechaMovimiento = Carbon::createFromFormat('d/m/Y', date('d/m/Y'))->startOfDay();
		$fechaSaldo = Carbon::createFromFormat('d/m/Y', date('d/m/Y'))->startOfDay();
		if($request->has('preliquidar')) {
			$reglas = [
				'socio_id'				=> [
											'bail',
											'required',
											'exists:sqlsrv.socios.socios,id,deleted_at,NULL'
										],
				'fechaMovimiento'		=> 'bail|required|date_format:"d/m/Y"',
				'fechaSaldo'			=> 'bail|required|date_format:"d/m/Y"',
			];
			$mensajes = [
				'socio_id.regex'		=> 'El :attribute no existe',
			];
			$atributos = [
				'socio_id'				=> 'socio',
			];
			$validator = Validator::make($request->all(), $reglas, $mensajes, $atributos);
			$socio = Socio::find($request->socio_id);
			if($socio != null) {
				$validator->after(function($validator) use($socio) {
					if ($socio->tercero->entidad_id != $this->getEntidad()->id) {
						$validator->errors()->add('socio_id', 'El socio no existe');
					}
				});
			}

			if($validator->fails()) {
				return redirect('retiroSocio/preliquidacion')->withErrors($validator)
										->withPreliquidacion($preliquidacion)
										->withInput();
			}
			$fechaMovimiento = Carbon::createFromFormat('d/m/Y', $request->fechaMovimiento)->startOfDay();
			$fechaSaldo = Carbon::createFromFormat('d/m/Y', $request->fechaSaldo)->startOfDay();
			$preliquidacion = DB::select("exec socios.sp_preliquidacion_retiro_socios ?, ?, ?", [$socio->id, $fechaMovimiento, $fechaSaldo]);

			if(!empty($preliquidacion[0]->ERROR)) {
				Session::flash('error', $preliquidacion[0]->MENSAJE);
				return view('socios.retiroSocio.preliquidacion')->withPreliquidacion(collect([]))->withSocio($socio);
			}
			foreach($preliquidacion as &$pre) {
				$pre->saldo = floatval($pre->saldo);
				$pre->interes = floatval($pre->interes);
			}
		}
		$preliquidacion= collect($preliquidacion);
		return view('socios.retiroSocio.preliquidacion')
							->withPreliquidacion($preliquidacion)
							->withSocio($socio)
							->withFechaMovimiento($fechaMovimiento)
							->withFechaSaldo($fechaSaldo);
	}

	public function retiroSocioLiquidar(Socio $obj, Request $request) {
		$this->log(sprintf("Liquidó el socio %s con los siguientes parámetros %s", $obj->tercero->nombre, json_encode($request->all())), 'ACTUALIZAR');
		Validator::make($request->all(), [
			'fecha_movimiento'		=> 'bail|required|date_format:"d/m/Y"',
			'fecha_saldo'			=> 'bail|required|date_format:"d/m/Y"',
		])->validate();
		if($this->moduloCerrado(2, $request->fecha_movimiento)) {
			return redirect()->back()
				->withErrors(['fechaMovimiento' => 'El módulo de contabilidad se encuentra cerrado para la fecha'])
				->withInput();
		}
		if($this->moduloCerrado(2, $request->fecha_saldo)) {
			return redirect()->back()
				->withErrors(['fechaSaldo' => 'El módulo de contabilidad se encuentra cerrado para la fecha'])
				->withInput();
		}
		if($this->moduloCerrado(6, $request->fecha_movimiento)) {
			return redirect()->back()
				->withErrors(['fechaMovimiento' => 'El módulo de ahorros se encuentra cerrado para la fecha'])
				->withInput();
		}
		if($this->moduloCerrado(6, $request->fecha_saldo)) {
			return redirect()->back()
				->withErrors(['fechaSaldo' => 'El módulo de ahorros se encuentra cerrado para la fecha'])
				->withInput();
		}
		if($this->moduloCerrado(7, $request->fecha_movimiento)) {
			return redirect()->back()
				->withErrors(['fechaMovimiento' => 'El módulo de cartera se encuentra cerrado para la fecha'])
				->withInput();
		}
		if($this->moduloCerrado(7, $request->fecha_saldo)) {
			return redirect()->back()
				->withErrors(['fechaSaldo' => 'El módulo de cartera se encuentra cerrado para la fecha'])
				->withInput();
		}

		$fechaMovimiento = Carbon::createFromFormat('d/m/Y', $request->fecha_movimiento)->startOfDay();
		$fechaSaldo = Carbon::createFromFormat('d/m/Y', $request->fecha_saldo)->startOfDay();
		$liquidacion = DB::select("exec socios.sp_liquidacion_retiro_socios ?, ?, ?", [$obj->id, $fechaMovimiento, $fechaSaldo]);

		if(!empty($liquidacion[0]->ERROR)) {
			Session::flash('error', $liquidacion[0]->MENSAJE);
		}
		else {
			Session::flash('message', $liquidacion[0]->MENSAJE);
		}
		return redirect('retiroSocio');
	}

	public function anular(SocioRetiro $obj) {
		if($this->moduloCerrado(10, $obj->fecha_solicitud_retiro)) {
			return redirect('retiroSocio')
				->withErrors(['error' => 'La fecha de retiro corresponde a un periodo ya cerrado']);
		}
		$socio = $obj->socio;
		if($socio->estado != "RETIRO") {
			return redirect('retiroSocio')
				->withErrors(['error' => 'El estado del asociado no es válido.']);
		}
		$this->log("Anuló la solicitud de retiro del socio " . $obj->socio->tercero->nombre_corto, "ACTUALIZAR");
		try {
			DB::beginTransaction();
			$obj->delete();
			
			$socio->estado = "ACTIVO";
			$socio->consecutivo_retiro--;
			$socio->fecha_ultimo_retiro = null;
			$socio->fecha_retiro = null;
			$socio->save();

			$usuarioWeb = $socio->usuarioWeb;
			$usuarioWeb->esta_activo = true;
			$usuarioWeb->save();
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Mensaje de error: ' . $e->getMessage());
			return redirect('retiroSocio')
				->withErrors(['error' => 'Error anulando el retiro.']);
		}
		$mensaje = "Se anulado la solicitud de retiro para '%s'";
		$mensaje = sprintf($mensaje, $socio->tercero->nombre_corto);
		Session::flash('message', $mensaje);
		return redirect('retiroSocio');
	}

	public function anularLiquidacion(SocioRetiro $obj) {
		$socio = $obj->socio;
		if($socio->estado != "LIQUIDADO") {
			return redirect('retiroSocio')
				->withErrors(['error' => 'El estado del asociado no es válido.']);
		}

		if($this->moduloCerrado(10, $obj->fecha_liquidacion)) {
			return redirect('retiroSocio')
				->withErrors(['error' => 'La fecha de retiro corresponde a un periodo ya cerrado']);
		}

		if($this->moduloCerrado(2, $obj->fecha_liquidacion)) {
			return redirect('retiroSocio')
				->withErrors(['error' => 'La fecha de retiro corresponde a un periodo ya cerrado']);
		}
		$this->log("Anuló la liquidación del socio " . $obj->socio->tercero->nombre_corto, "ACTUALIZAR");

		$usuario = Auth::user();

		$anulacion = DB::select("exec socios.sp_anular_liquidacion_retiro_socios ?, ?, ?", [$obj->id, $usuario->usuario, $usuario->nombreCompleto]);

		if(!empty($anulacion[0]->ERROR)) {
			Session::flash('error', $anulacion[0]->MENSAJE);
		}
		else {
			Session::flash('message', $anulacion[0]->MENSAJE);
		}
		return redirect('retiroSocio');
	}

	public static function routes() {
		Route::get('retiroSocio', 'Socios\RetiroSocioController@index');
		Route::get('retiroSocio/create', 'Socios\RetiroSocioController@create');
		Route::post('retiroSocio', 'Socios\RetiroSocioController@store');
		Route::get('retiroSocio/preliquidacion', 'Socios\RetiroSocioController@preliquidacion');
		Route::put('retiroSocio/{obj}/liquidar', 'Socios\RetiroSocioController@retiroSocioLiquidar')->name('retiroSocioLiquidar');
		Route::delete('retiroSocio/{obj}', 'Socios\RetiroSocioController@anular')->name("retiroSocio.anular");
		Route::put('retiroSocio/{obj}', 'Socios\RetiroSocioController@anularLiquidacion')->name("retiroSocio.anularLiquidacion");
	}
}
