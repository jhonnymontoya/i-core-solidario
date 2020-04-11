<?php

namespace App\Http\Controllers\Recaudos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recaudos\Pagaduria\CreatePagaduriaRequest;
use App\Http\Requests\Recaudos\Pagaduria\EditPagaduriaRequest;
use App\Models\General\Tercero;
use App\Models\Recaudos\CalendarioRecaudo;
use App\Models\Recaudos\Pagaduria;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class PagaduriaController extends Controller
{
	use FonadminTrait;

	private $periodicidades = array(
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

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$pagadurias = Pagaduria::entidadId()->search($request->name)->paginate();
		return view('recaudos.pagaduria.index')->withPagadurias($pagadurias);
	}

	public function create() {
		return view('recaudos.pagaduria.create')->withPeriodicidades($this->periodicidades);
	}

	public function store(CreatePagaduriaRequest $request) {
		//Se busca el tercero con el NIT dado
		$tercero = Tercero::entidadTercero()
			->whereTipoTercero('JURÍDICA')
			->whereTipoIdentificacionId(2)
			->whereNumeroIdentificacion($request->nit)
			->first();

		if(!$tercero) {
			$tercero = new Tercero;
			$tercero->entidad_id = $this->getEntidad()->id;
			$tercero->tipo_tercero = 'JURÍDICA';
			$tercero->tipo_identificacion_id = 2;
			$tercero->numero_identificacion = $request->nit;
			$tercero->razon_social = $request->razonSocial;
			$tercero->save();
		}

		$pagaduria = new Pagaduria;
		$pagaduria->fill($request->all());
		$pagaduria->entidad_id = $this->getEntidad()->id;
		$pagaduria->tercero_empresa_id = $tercero->id;
		if(empty($request->ciudad_id)) {
			$pagaduria->ciudad_id = null;
		}
		$pagaduria->save();
		Session::flash('message', 'Se ha creado la pagaduría \'' . $pagaduria->nombre . '\'');
		return redirect()->route('pagaduriaEdit', $pagaduria->id);
	}

	public function edit(Pagaduria $obj) {
		return view('recaudos.pagaduria.edit')->withPagaduria($obj)->withPeriodicidades($this->periodicidades);
	}

	public function update(EditPagaduriaRequest $request, Pagaduria $obj) {
		if(!empty($request->programar) && $request->programar == 'true') {
			if($obj->calendarioRecaudos()->whereEstado('EJECUTADO')->count() == 0) {
				$obj->fecha_inicio_recaudo = $request->fecha_inicio_recaudo;
				$obj->fecha_inicio_reporte = $request->fecha_inicio_reporte;
				$obj->save();
			}
			$this->getCalendarioRecaudos($obj, $request->anioPeriodo);
			Session::flash('message', 'Se ha actualizado la pagaduría \'' . $obj->nombre . '\'');
			return redirect()->route('pagaduriaEdit', $obj->id);
		}
		//Se busca el tercero con el NIT dado
		$tercero = Tercero::entidadTercero()
							->whereTipoTercero('JURÍDICA')
							->whereTipoIdentificacionId(2)
							->whereNumeroIdentificacion($request->nit)
							->first();
		if(!$tercero) {
			$tercero = new Tercero;
			$tercero->entidad_id = $this->getEntidad()->id;
			$tercero->tipo_tercero = 'JURÍDICA';
			$tercero->tipo_identificacion_id = 2;
			$tercero->numero_identificacion = $request->nit;
			$tercero->razon_social = $request->razonSocial;
			$tercero->save();
		}
		$obj->fill($request->all());
		$obj->entidad_id = $this->getEntidad()->id;
		$obj->tercero_empresa_id = $tercero->id;
		if(empty($request->ciudad_id)) {
			$obj->ciudad_id = null;
		}
		$obj->save();
		Session::flash('message', 'Se ha actualizado la pagaduría \'' . $obj->nombre . '\'');
		return redirect('pagaduria');
	}

	public function getCalendarioRecaudos($pagaduria, $anioInicio) {
		$periodicidad = $pagaduria->periodicidad_pago;
		$inicioAnio = new Carbon('first day of January ' . $anioInicio, config('app.timezone'));
		$finAnio = new Carbon('last day of December ' . $anioInicio, config('app.timezone'));
		if($pagaduria->calendarioRecaudos->count()) {
			$ultimoPeriodo = $pagaduria->calendarioRecaudos()->orderBy('fecha_recaudo', 'desc')->first();
			$fechaInicioRecaudo = $this->getSiguientePeriodo($ultimoPeriodo->fecha_recaudo->copy(), $periodicidad);
			$fechaInicioReporte = $this->getSiguientePeriodo($ultimoPeriodo->fecha_reporte->copy(), $periodicidad);
		}
		else {
			$fechaInicioRecaudo = $pagaduria->fecha_inicio_recaudo->copy()->startOfDay();
			$fechaInicioReporte = $pagaduria->fecha_inicio_reporte->copy()->startOfDay();
		}
		$periodos = array();
		switch($periodicidad) {
			case 'DIARIO':
				do {
					$tmp = array();

					$tmp['numero_periodo'] = $fechaInicioRecaudo->diffInDays($inicioAnio) + 1;
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';

					$fechaInicioReporte->addDay();

					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->addDay()->lte($finAnio));
				break;
			case 'SEMANAL':
				do {
					$tmp = array();
					$tmp['numero_periodo'] = $fechaInicioRecaudo->weekOfYear;
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					$fechaInicioReporte->addWeek();
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->addWeek()->lte($finAnio));
				break;
			case 'DECADAL':
				do {
					$periodo = ($fechaInicioRecaudo->month - 1) * 3;
					if($fechaInicioRecaudo->day <= 10) $periodo += 1;
					elseif($fechaInicioRecaudo->day <= 20) $periodo += 2;
					else $periodo += 3;
					$tmp = array();
					$tmp['numero_periodo'] = $periodo;
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					$fechaInicioReporte->addDays(10);
					array_push($periodos, $tmp);
					if($fechaInicioRecaudo->day < 10) {
						$fechaInicioRecaudo->addDays(10 - $fechaInicioRecaudo->day);
					}
					elseif($fechaInicioRecaudo->day < 20) {
						$fechaInicioRecaudo->addDays(20 - $fechaInicioRecaudo->day);
					}
					elseif($fechaInicioRecaudo->day >= 20 && $fechaInicioRecaudo->day < $fechaInicioRecaudo->daysInMonth) {
						$fechaInicioRecaudo->endOfMonth()->startOfDay();
					}
					else {
						$fechaInicioRecaudo->addDays(10)->startOfDay();
					}
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'CATORCENAL':
				do {
					$tmp = array();
					$tmp['numero_periodo'] = ceil($fechaInicioRecaudo->weekOfYear / 2);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					$fechaInicioReporte->addWeek(2);
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->addWeek(2)->lte($finAnio));
				break;
			case 'QUINCENAL':
				$diasDiferencia = $fechaInicioRecaudo->diffInDays($fechaInicioReporte);
				do {
					$tmp = array();
					if($fechaInicioRecaudo->day < 15) {
						$fechaInicioRecaudo->day = 15;
					}
					else {
						$mes = $fechaInicioRecaudo->month;
						while($fechaInicioRecaudo->month == $mes)$fechaInicioRecaudo->addDay();
						$fechaInicioRecaudo->subDay();
					}
					$fechaInicioReporte = $fechaInicioRecaudo->copy();
					$fechaInicioReporte->subDays($diasDiferencia + 1);
					$tmp['numero_periodo'] = $fechaInicioRecaudo->day <= 15 ? ($fechaInicioRecaudo->month * 2) - 1 : ($fechaInicioRecaudo->month * 2);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					$fechaInicioRecaudo->addDay();
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'MENSUAL':
				$esFinDeMes = $fechaInicioRecaudo->day == $fechaInicioRecaudo->daysInMonth ? true : false;
				$dia = $fechaInicioRecaudo->day;
				$esFinDeMesReporte = $fechaInicioReporte->day == $fechaInicioReporte->daysInMonth ? true : false;
				$diaReporte = $fechaInicioReporte->day;
				do {
					$tmp = array();
					$tmp['numero_periodo'] = $fechaInicioRecaudo->month;
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					if($esFinDeMes) {
						$fechaInicioRecaudo->addDay();
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth;
					}
					elseif($dia > 28) {
						$fechaInicioRecaudo->addDays(15);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth <= $dia ? $fechaInicioRecaudo->daysInMonth : $dia;
					}
					else {
						$fechaInicioRecaudo->addMonth();
					}
					if($esFinDeMesReporte) {
						$fechaInicioReporte->addDay();
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth;
					}
					elseif($diaReporte > 28) {
						$fechaInicioReporte->addDays(15);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth <= $diaReporte ? $fechaInicioReporte->daysInMonth : $diaReporte;
					}
					else {
						$fechaInicioReporte->addMonth();
					}
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'BIMESTRAL':
				$esFinDeMes = $fechaInicioRecaudo->day == $fechaInicioRecaudo->daysInMonth ? true : false;
				$dia = $fechaInicioRecaudo->day;
				$esFinDeMesReporte = $fechaInicioReporte->day == $fechaInicioReporte->daysInMonth ? true : false;
				$diaReporte = $fechaInicioReporte->day;
				do {
					$tmp = array();
					$tmp['numero_periodo'] = ceil($fechaInicioRecaudo->month / 2);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					if($esFinDeMes) {
						$fechaInicioRecaudo->addDay()->addMonth();
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth;
					}
					elseif($dia > 28) {
						$fechaInicioRecaudo->addDays(45);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth <= $dia ? $fechaInicioRecaudo->daysInMonth : $dia;
					}
					else {
						$fechaInicioRecaudo->addMonths(2);
					}
					if($esFinDeMesReporte) {
						$fechaInicioReporte->addDay()->addMonth();
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth;
					}
					elseif($diaReporte > 28) {
						$fechaInicioReporte->addDays(45);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth <= $diaReporte ? $fechaInicioReporte->daysInMonth : $diaReporte;
					}
					else {
						$fechaInicioReporte->addMonths(2);
					}
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'TRIMESTRAL':
				$esFinDeMes = $fechaInicioRecaudo->day == $fechaInicioRecaudo->daysInMonth ? true : false;
				$dia = $fechaInicioRecaudo->day;
				$esFinDeMesReporte = $fechaInicioReporte->day == $fechaInicioReporte->daysInMonth ? true : false;
				$diaReporte = $fechaInicioReporte->day;
				do {
					$tmp = array();
					$tmp['numero_periodo'] = ceil($fechaInicioRecaudo->month / 3);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					if($esFinDeMes) {
						$fechaInicioRecaudo->addDay()->addMonths(2);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth;
					}
					elseif($dia > 28) {
						$fechaInicioRecaudo->addDays(75);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth <= $dia ? $fechaInicioRecaudo->daysInMonth : $dia;
					}
					else {
						$fechaInicioRecaudo->addMonths(3);
					}
					if($esFinDeMesReporte) {
						$fechaInicioReporte->addDay()->addMonths(2);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth;
					}
					elseif($diaReporte > 28) {
						$fechaInicioReporte->addDays(75);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth <= $diaReporte ? $fechaInicioReporte->daysInMonth : $diaReporte;
					}
					else {
						$fechaInicioReporte->addMonths(3);
					}
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'CUATRIMESTRAL':
				$esFinDeMes = $fechaInicioRecaudo->day == $fechaInicioRecaudo->daysInMonth ? true : false;
				$dia = $fechaInicioRecaudo->day;
				$esFinDeMesReporte = $fechaInicioReporte->day == $fechaInicioReporte->daysInMonth ? true : false;
				$diaReporte = $fechaInicioReporte->day;
				do {
					$tmp = array();
					$tmp['numero_periodo'] = ceil($fechaInicioRecaudo->month / 4);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					if($esFinDeMes) {
						$fechaInicioRecaudo->addDay()->addMonths(3);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth;
					}
					elseif($dia > 28) {
						$fechaInicioRecaudo->addDays(105);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth <= $dia ? $fechaInicioRecaudo->daysInMonth : $dia;
					}
					else {
						$fechaInicioRecaudo->addMonths(4);
					}
					if($esFinDeMesReporte) {
						$fechaInicioReporte->addDay()->addMonths(3);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth;
					}
					elseif($diaReporte > 28) {
						$fechaInicioReporte->addDays(105);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth <= $diaReporte ? $fechaInicioReporte->daysInMonth : $diaReporte;
					}
					else {
						$fechaInicioReporte->addMonths(4);
					}
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'SEMESTRAL':
				$esFinDeMes = $fechaInicioRecaudo->day == $fechaInicioRecaudo->daysInMonth ? true : false;
				$dia = $fechaInicioRecaudo->day;
				$esFinDeMesReporte = $fechaInicioReporte->day == $fechaInicioReporte->daysInMonth ? true : false;
				$diaReporte = $fechaInicioReporte->day;
				do {
					$tmp = array();
					$tmp['numero_periodo'] = ceil($fechaInicioRecaudo->month / 6);
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					if($esFinDeMes) {
						$fechaInicioRecaudo->addDay()->addMonths(5);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth;
					}
					elseif($dia > 28) {
						$fechaInicioRecaudo->addDays(165);
						$fechaInicioRecaudo->day = $fechaInicioRecaudo->daysInMonth <= $dia ? $fechaInicioRecaudo->daysInMonth : $dia;
					}
					else {
						$fechaInicioRecaudo->addMonths(6);
					}
					if($esFinDeMesReporte) {
						$fechaInicioReporte->addDay()->addMonths(5);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth;
					}
					elseif($diaReporte > 28) {
						$fechaInicioReporte->addDays(165);
						$fechaInicioReporte->day = $fechaInicioReporte->daysInMonth <= $diaReporte ? $fechaInicioReporte->daysInMonth : $diaReporte;
					}
					else {
						$fechaInicioReporte->addMonths(6);
					}
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->lte($finAnio));
				break;
			case 'ANUAL':
				do {
					$tmp = array();
					$tmp['numero_periodo'] = 1;
					$tmp['fecha_recaudo'] = $fechaInicioRecaudo->copy();
					$tmp['fecha_reporte'] = $fechaInicioReporte->copy();
					$tmp['estado'] = 'PROGRAMADO';
					$fechaInicioReporte->addYear();
					array_push($periodos, $tmp);
				}
				while($fechaInicioRecaudo->addYear()->lte($finAnio));
				break;
		}
		$pagaduria->calendarioRecaudos()->createMany($periodos);
	}

	public function getSiguientePeriodo($fecha, $periodicidad) {
		$nuevaFecha = "";
		switch ($periodicidad) {
			case 'ANUAL':
				$fecha->addYear();
				$nuevaFecha = $fecha;
				break;
			case 'SEMESTRAL':
				$esFinDeMes = $fecha->day == $fecha->daysInMonth ? true : false;
				$dia = $fecha->day;
				if($esFinDeMes) {
					$fecha->addDay()->addMonths(5);
					$fecha->day = $fecha->daysInMonth;
				}
				elseif($dia > 28) {
					$fecha->addDays(165);
					$fecha->day = $fecha->daysInMonth <= $dia ? $fecha->daysInMonth : $dia;
				}
				else {
					$fecha->addMonths(6);
				}
				$nuevaFecha = $fecha;
				break;
			case 'CUATRIMESTRAL':
				$esFinDeMes = $fecha->day == $fecha->daysInMonth ? true : false;
				$dia = $fecha->day;
				if($esFinDeMes) {
					$fecha->addDay()->addMonths(3);
					$fecha->day = $fecha->daysInMonth;
				}
				elseif($dia > 28) {
					$fecha->addDays(105);
					$fecha->day = $fecha->daysInMonth <= $dia ? $fecha->daysInMonth : $dia;
				}
				else {
					$fecha->addMonths(4);
				}
				$nuevaFecha = $fecha;
				break;
			case 'TRIMESTRAL':
				$esFinDeMes = $fecha->day == $fecha->daysInMonth ? true : false;
				$dia = $fecha->day;
				if($esFinDeMes) {
					$fecha->addDay()->addMonths(2);
					$fecha->day = $fecha->daysInMonth;
				}
				elseif($dia > 28) {
					$fecha->addDays(75);
					$fecha->day = $fecha->daysInMonth <= $dia ? $fecha->daysInMonth : $dia;
				}
				else {
					$fecha->addMonths(3);
				}
				$nuevaFecha = $fecha;
				break;
			case 'BIMESTRAL':
				$esFinDeMes = $fecha->day == $fecha->daysInMonth ? true : false;
				$dia = $fecha->day;
				if($esFinDeMes) {
					$fecha->addDay()->addMonth();
					$fecha->day = $fecha->daysInMonth;
				}
				elseif($dia > 28) {
					$fecha->addDays(45);
					$fecha->day = $fecha->daysInMonth <= $dia ? $fecha->daysInMonth : $dia;
				}
				else {
					$fecha->addMonths(2);
				}
				$nuevaFecha = $fecha;
				break;
			case 'MENSUAL':
				$fecha->addMonth();
				$nuevaFecha = $fecha;
				break;
			case 'QUINCENAL':
				$nuevaFecha = $fecha->addDay();
				break;
			case 'CATORCENAL':
				$fecha->addWeeks(2);
				$nuevaFecha = $fecha;
				break;
			case 'DECADAL':
				$fecha->addDays(10);
				$nuevaFecha = $fecha;
				break;
			case 'SEMANAL':
				$fecha->addWeek();
				$nuevaFecha = $fecha;
				break;
			case 'DIARIO':
				$nuevaFecha = $fecha->addDay();
				break;			
			default:
				$nuevaFecha = $fecha;
				break;
		}
		return $nuevaFecha;
	}

	public static function routes() {
		Route::get('pagaduria', 'Recaudos\PagaduriaController@index');
		Route::get('pagaduria/create', 'Recaudos\PagaduriaController@create');
		Route::post('pagaduria', 'Recaudos\PagaduriaController@store');
		Route::get('pagaduria/{obj}/edit', 'Recaudos\PagaduriaController@edit')->name('pagaduriaEdit');
		Route::put('pagaduria/{obj}', 'Recaudos\PagaduriaController@update');
	}
}
