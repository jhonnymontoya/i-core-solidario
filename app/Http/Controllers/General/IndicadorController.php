<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Indicador\CreateIndicadorRequest;
use App\Http\Requests\General\Indicador\EditIndicadorRequest;
use App\Models\General\Indicador;
use App\Models\General\TipoIndicador;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class IndicadorController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$tiposIndicadores = TipoIndicador::entidadId()->orderBy('codigo')->pluck('codigo', 'id');
		$tipoIndicador = TipoIndicador::entidadId()->whereId($request->indicador)->first();
		$indicadores = Indicador::tipoIndicadorId($request->indicador)->orderBy('fecha_inicio', 'desc')->paginate();

		return view('general.indicador.index')
					->withTiposIndicadores($tiposIndicadores)
					->withIndicadores($indicadores)
					->withTipoIndicador($tipoIndicador);
	}

	public function create(TipoIndicador $obj) {
		$indicador = Indicador::tipoIndicadorId($obj->id)->orderBy('fecha_inicio', 'desc')->first();
		return view('general.indicador.create')
					->withTipoIndicador($obj)
					->withIndicador($indicador);
	}

	public function store(TipoIndicador $obj, CreateIndicadorRequest $request) {
		$indicador = new Indicador;
		if($obj->indicadores->count()) {
			$indicadorAnterior = Indicador::tipoIndicadorId($obj->id)->orderBy('fecha_inicio', 'desc')->first();
			$indicador->fill($request->all());
			$indicador->fecha_inicio = $indicadorAnterior->fecha_fin->addDay();
			$indicador->fecha_fin = $this->getPeriodos(clone $indicador->fecha_inicio, $obj->periodicidad);
		}
		else {
			$indicador->fill($request->all());
			$indicador->fecha_fin = $this->getPeriodos(clone $indicador->fecha_inicio, $obj->periodicidad);
		}
		$obj->indicadores()->save($indicador);
		Session::flash('message', 'Se ha actualizado el indicador');
		return redirect('indicador?indicador=' . $obj->id);
	}

	public function edit(Indicador $obj) {
		return view('general.indicador.edit')->withIndicador($obj);
	}

	public function update(Indicador $obj, EditIndicadorRequest $request) {
		$obj->valor = $request->valor;
		$obj->save();
		Session::flash('message', 'Se ha actualizado el indicador');
		return redirect('indicador?indicador=' . $obj->tipoIndicador->id);
	}

	public function periodos(TipoIndicador $obj, Request $request) {
		$validator = Validator::make($request->all(), ['fecha_inicio' => 'bail|required|date_format:"d/m/Y"'], ['date_format' => 'La fecha debe estar en formato dd/mm/yyyy']);
		if($validator->fails()) {
			return response()->json(["fecha_fin" => ""]);
		}
		$fechaInicio = Carbon::createFromFormat('d/m/Y', $request->fecha_inicio)->startOfDay();
		$fechaFin = $this->getPeriodos($fechaInicio, $obj->periodicidad);
		return response()->json(["fecha_fin" => $fechaFin]);
	}

	public function getPeriodos($fechaInicio, $periodicidad) {
		$fechaFin = "";
		switch ($periodicidad) {
			case 'ANUAL':
				$fechaInicio->addYear();
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'SEMESTRAL':
				$fechaInicio->addMonths(6);
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'CUATRIMESTRAL':
				$fechaInicio->addMonths(4);
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'TRIMESTRAL':
				$fechaInicio->addMonths(3);
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'BIMESTRAL':
				$fechaInicio->addMonths(2);
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'MENSUAL':
				$fechaInicio->addMonth();
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'QUINCENAL':
				if($fechaInicio->day != 1 && $fechaInicio->day != 16) {
					$fechaFin = "";
					break;
				}
				if($fechaInicio->day < 15) {
					$fechaInicio->addDays(14);
				}
				else {
					$mes = $fechaInicio->month;
					while($fechaInicio->month == $mes)$fechaInicio->addDay();
					$fechaInicio->subDay();
				}
				$fechaFin = $fechaInicio;
				break;
			case 'CATORCENAL':
				$fechaInicio->addWeeks(2);
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'DECADAL':
				if($fechaInicio->day < 10) {
					$fechaFin->addDays(10 - $fechaInicio->day);
				}
				elseif($fechaInicio->day < 20) {
					$fechaFin->addDays(20 - $fechaInicio->day);
				}
				else {
					$fechaFin->endOfMonth()->startOfDay();
				}
				break;
			case 'SEMANAL':
				$fechaInicio->addWeek();
				$fechaInicio->subDay();
				$fechaFin = $fechaInicio;
				break;
			case 'DIARIO':
				$fechaFin = $fechaInicio;
				break;
			default:
				$fechaFin = $fechaInicio;
				break;
		}
		return $fechaFin->format("d/m/Y");
	}

	public static function routes() {
		Route::get('indicador', 'General\IndicadorController@index');
		Route::get('indicador/{obj}/create', 'General\IndicadorController@create')->name('indicadorCreate');
		Route::post('indicador/{obj}', 'General\IndicadorController@store');
		Route::get('indicador/{obj}/edit', 'General\IndicadorController@edit')->name('indicadorEdit');
		Route::put('indicador/{obj}', 'General\IndicadorController@update');

		Route::get('indicador/{obj}/periodos', 'General\IndicadorController@periodos')->name('indicadorPeriodoGet');
	}
}
