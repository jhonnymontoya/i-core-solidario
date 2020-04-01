<?php

namespace App\Http\Controllers\Reportes;

use App\Helpers\ConversionHelper;
use App\Http\Controllers\Controller;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Contabilidad\Impuesto;
use App\Models\Contabilidad\Modulo;
use App\Models\Contabilidad\Movimiento;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ParametroInstitucional;
use App\Models\General\Reporte;
use App\Models\General\Tercero;
use App\Models\Recaudos\ControlProceso;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class ReportesController extends Controller
{
	use FonadminTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu')->except(['balancePrueba', 'balancePruebaImprimir']);
	}

	public function index(Request $request) {
		$request->validate([
			'name'		=> 'bail|nullable|string|max:100',
			'modulo'	=> 'bail|nullable|integer'
		]);
		$reportes = Reporte::activo()->categoria($request->modulo)->search($request->name)->orderBy('categoria_modulo_id')->orderBy('nombre')->paginate();
		$modulos = Modulo::activo()->orderBy('nombre')->pluck('nombre', 'id');
		return view('reportes.index')->withModulos($modulos)->withReportes($reportes);
	}

	public function reportesEstadisticos(Request $request) {
		$req = $request->validate([
			'tipo_reporte' => 'bail|nullable|string|max:100',
			'fecha_consulta' => 'bail|nullable|date_format:"Y/m"'
		]);
		$vista = '';
		$fecha = date('Y/m/d');
		$data = '';
		try {
			if (!empty($req["fecha_consulta"])) {
				$fecha = Carbon::createFromFormat(
					'Y/m/d',
					$req["fecha_consulta"] . "/01"
				)
				->endOfMonth()
				->startOfDay();
			}
			else {
				$fecha = Carbon::createFromFormat('Y/m/d', $fecha )
				->endOfMonth()
				->startOfDay();
			}
		}
		catch(\InvalidArgumentException $e) {
			$vista = '';
		}//dd($request->all(), $fecha, "kkkkk");
		switch($request->tipo_reporte) {
			case 'ASOCIADOS' : {
				$vista = $this->estadisticoAsociados($fecha);
				break;
			}
			case 'AHORROS' : {
				$vista = $this->estadisticoAhorros($fecha);
				break;
			}
			case 'CARTERA' : {
				$vista = $this->estadisticoCartera($fecha);
				break;
			}
			default : {
				break;
			}
		}
		return view('reportes.estadisticos')->withData($vista);
	}

	/**
	 * Reportes estadísticos de asociados
	 * @return type
	 */
	public function estadisticoAsociados($fecha) {
		$entidad = $this->getEntidad();
		//Asociados por genero
		$sql = "SELECT se.nombre genero, COUNT(s.id) cantidad FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id INNER JOIN general.sexos AS se ON t.sexo_id = se.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (s.fecha_retiro IS NULL OR DATEADD(dd, DATEDIFF(dd, 0, s.fecha_retiro), 0) > ?) GROUP BY se.nombre";
		$asociadosPorGenero = DB::select($sql, [$entidad->id, $fecha, $fecha]);
		$cant = 0;
		foreach ($asociadosPorGenero as $value) $cant += $value->cantidad;
		if($cant == 0) {
			foreach ($asociadosPorGenero as $value) $value->porcentaje = 0;
		}
		else {
			foreach ($asociadosPorGenero as $value) $value->porcentaje = round(($value->cantidad * 100) / $cant, 2);
		}

		//Asociados por pagaduría
		$sql = "SELECT p.nombre pagaduria, COUNT(s.id) cantidad FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id INNER JOIN recaudos.pagadurias as p ON s.pagaduria_id = p.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (s.fecha_retiro IS NULL OR DATEADD(dd, DATEDIFF(dd, 0, s.fecha_retiro), 0) > ?) GROUP BY p.nombre ORDER BY COUNT(s.id) DESC";
		$asociadosPorPagaduria = DB::select($sql, [$entidad->id, $fecha, $fecha]);
		$cantidadAsociados = 0;
		foreach ($asociadosPorPagaduria as $value) $cantidadAsociados += $value->cantidad;
		if($cantidadAsociados == 0) {
			foreach ($asociadosPorPagaduria as $value) $value->porcentaje = 0;
		}
		else {
			foreach ($asociadosPorPagaduria as $value) $value->porcentaje = round(($value->cantidad * 100) / $cantidadAsociados, 2);
		}

		//Fecha antiguedad
		$sql = "SELECT s.fecha_antiguedad fecha_antiguedad FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (s.fecha_retiro IS NULL OR DATEADD(dd, DATEDIFF(dd, 0, s.fecha_retiro), 0) > ?)";
		$asociadosPorAntiguedad = collect();
		$asociadosPorAntiguedad->put(0, (object)array("nombre" => "Mayor a 5 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorAntiguedad->put(1, (object)array("nombre" => "Entre 3 y 5 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorAntiguedad->put(2, (object)array("nombre" => "Entre 1 y 3 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorAntiguedad->put(3, (object)array("nombre" => "Hasta 1 año", "cantidad" => 0, "porcentaje" => 0));
		$res = DB::select($sql, [$entidad->id, $fecha, $fecha]);
		$cantidadAsociados = 0;
		foreach ($res as $value) {
			$cantidadAsociados++;
			$diff = substr($value->fecha_antiguedad, 0, 10);
			$diff = Carbon::createFromFormat('Y-m-d', $diff)->startOfDay();
			$diff = $diff->diffInYears($fecha);
			if($diff >= 5) {
				$asociadosPorAntiguedad[0]->cantidad++;
			}
			elseif($diff < 5 && $diff >= 3) {
				$asociadosPorAntiguedad[1]->cantidad++;
			}
			elseif($diff < 3 && $diff >= 1) {
				$asociadosPorAntiguedad[2]->cantidad++;
			}
			else {
				$asociadosPorAntiguedad[3]->cantidad++;
			}
		}
		if($cantidadAsociados > 0) {
			foreach ($asociadosPorAntiguedad as &$value)$value->porcentaje = round(($value->cantidad * 100) / $cantidadAsociados, 2);
		}

		//Edad
		$sql = "SELECT t.fecha_nacimiento fecha_nacimiento FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (s.fecha_retiro IS NULL OR DATEADD(dd, DATEDIFF(dd, 0, s.fecha_retiro), 0) > ?)";
		$asociadosPorEdad = collect();
		$asociadosPorEdad->put(0, (object)array("nombre" => "Mayor a 50 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorEdad->put(1, (object)array("nombre" => "Entre 40 y 50 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorEdad->put(2, (object)array("nombre" => "Entre 30 y 40 años", "cantidad" => 0, "porcentaje" => 0));
		$asociadosPorEdad->put(3, (object)array("nombre" => "Hasta 30 años", "cantidad" => 0, "porcentaje" => 0));
		$res = DB::select($sql, [$entidad->id, $fecha, $fecha]);
		$cantidadAsociados = 0;
		foreach ($res as $value) {
			$cantidadAsociados++;
			$diff = substr($value->fecha_nacimiento, 0, 10);
			$diff = Carbon::createFromFormat('Y-m-d', $diff)->startOfDay();
			$diff = $diff->diffInYears($fecha);
			if($diff >= 50) {
				$asociadosPorEdad[0]->cantidad++;
			}
			elseif($diff < 50 && $diff >= 40) {
				$asociadosPorEdad[1]->cantidad++;
			}
			elseif($diff < 40 && $diff >= 30) {
				$asociadosPorEdad[2]->cantidad++;
			}
			else {
				$asociadosPorEdad[3]->cantidad++;
			}
		}
		if($cantidadAsociados > 0) {
			foreach ($asociadosPorEdad as &$value)$value->porcentaje = round(($value->cantidad * 100) / $cantidadAsociados, 2);
		}

		//Asociados sin créditos
		$sql = "WITH saloCero AS(SELECT sc.tercero_id, SUM(mcc.valor_movimiento) valor FROM creditos.movimientos_capital_credito AS mcc INNER JOIN creditos.solicitudes_creditos AS sc ON mcc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? GROUP BY sc.tercero_id HAVING SUM(mcc.valor_movimiento) > 0 ) SELECT se.nombre genero, COUNT(s.id) cantidad FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id INNER JOIN general.sexos AS se ON t.sexo_id = se.id LEFT JOIN saloCero AS sc ON sc.tercero_id = t.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (s.fecha_retiro IS NULL OR DATEADD(dd, DATEDIFF(dd, 0, s.fecha_retiro), 0) > ?) AND sc.tercero_id IS NULL GROUP BY se.nombre";
		$asociadosSinCreditos = DB::select($sql, [$entidad->id, $entidad->id, $fecha, $fecha]);
		if($cant == 0) {
			foreach ($asociadosSinCreditos as $value) $value->porcentaje = 0;
		}
		else {
			foreach ($asociadosSinCreditos as $value) $value->porcentaje = round(($value->cantidad * 100) / $cant, 2);
		}

		//afiliaciones del mes
		$sql = "SELECT t.numero_identificacion, t.nombre, s.fecha_afiliacion, p.nombre pagaduria, SUM(COALESCE(CASE co.tipo_calculo WHEN 'VALORFIJO' THEN co.valor WHEN 'PORCENTAJESUELDO' THEN (co.valor * s.sueldo_mes) / 100 END, 0)) AS cuota_oblogatoria, SUM(COALESCE(CASE cv.factor_calculo WHEN 'VALORFIJO' THEN general.fn_conversion_valor_periodicidad(cv.valor, cv.periodicidad, 'MENSUAL') WHEN 'PORCENTAJESUELDO' THEN general.fn_conversion_valor_periodicidad((cv.valor * s.sueldo_mes) / 100, cv.periodicidad, 'MENSUAL') END, 0)) AS cuota_voluntaria FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id INNER JOIN recaudos.pagadurias AS p ON s.pagaduria_id = p.id LEFT JOIN ahorros.cuotas_voluntarias AS cv ON cv.socio_id = s.id AND cv.deleted_at IS NULL LEFT JOIN socios.cuotas_obligatorias AS co ON co.socio_id = s.id WHERE t.entidad_id = ? AND s.fecha_afiliacion BETWEEN DATEADD(DAY, -(DAY(?) - 1), ?) AND ? GROUP BY t.numero_identificacion, t.nombre, s.fecha_afiliacion, p.nombre;";
		$asociadosDelMes = DB::select($sql, [$entidad->id, $fecha, $fecha, $fecha]);
		foreach ($asociadosDelMes as &$item) {
			$diff = substr($item->fecha_afiliacion, 0, 10);
			$item->fecha_afiliacion = Carbon::createFromFormat('Y-m-d', $diff)->startOfDay();
		}

		//retiros del mes
		$sql = "WITH saldoCartera AS( SELECT t.id, mcc.valor_movimiento, mcc.fecha_movimiento FROM creditos.movimientos_capital_credito AS mcc INNER JOIN creditos.solicitudes_creditos AS sc ON mcc.solicitud_credito_id = sc.id INNER JOIN general.terceros AS t ON sc.tercero_id = t.id INNER JOIN socios.socios AS s ON s.tercero_id = t.id WHERE t.entidad_id = ? ) SELECT DISTINCT t.numero_identificacion, t.nombre, s.fecha_retiro, cr.nombre causal, cr.tipo_causa_retiro, pa.nombre AS pagaduria, (SELECT COALESCE(SUM(ma.valor_movimiento), 0) FROM ahorros.movimientos_ahorros AS ma WHERE ma.socio_id = s.id AND ma.fecha_movimiento <= DATEADD(DAY, -1, s.fecha_retiro)) ahorros, (SELECT COALESCE(SUM(x.valor_movimiento), 0) FROM saldoCartera AS x WHERE x.id = t.id AND x.fecha_movimiento <= DATEADD(DAY, -1, s.fecha_retiro)) cartera FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id INNER JOIN socios.socios_retiros AS sr on sr.socio_id = s.id INNER JOIN socios.causas_retiro AS cr ON sr.causa_retiro_id = cr.id INNER JOIN recaudos.pagadurias AS pa ON s.pagaduria_id = pa.id WHERE t.entidad_id = ? AND s.fecha_retiro BETWEEN DATEADD(DAY, -(DAY(?) - 1), ?) AND ?;";
		$retirosDelMes = DB::select($sql, [$entidad->id, $entidad->id, $fecha, $fecha, $fecha]);
		foreach ($retirosDelMes as &$item) {
			$diff = substr($item->fecha_retiro, 0, 10);
			$item->fecha_retiro = Carbon::createFromFormat('Y-m-d', $diff)->startOfDay();
			$item->total = $item->ahorros + $item->cartera;
		}

		//afiliaciones por mes
		$sql = "WITH sub AS( SELECT YEAR(fecha_afiliacion) AS anio, MONTH(fecha_afiliacion) AS mes, COUNT(fecha_afiliacion) AS cantidad FROM socios.socios AS s INNER JOIN general.terceros AS t on s.tercero_id = t.id WHERE t.entidad_id = ? AND fecha_afiliacion BETWEEN DATEADD(DAY, -DAY(?), DATEADD(MONTH, -23, ?)) AND ? GROUP BY YEAR(fecha_afiliacion), MONTH(fecha_afiliacion) ) SELECT mes, cantidad FROM sub ORDER BY anio DESC, mes DESC";
		$afiliacionesPorMes = DB::select($sql, [$entidad->id, $fecha, $fecha, $fecha]);
		$capm = count($afiliacionesPorMes);
		for($mes = $afiliacionesPorMes[$capm - 1]->mes, $i = $capm; 24 > $capm; $capm++) {
			$mes--;
			$mes = ($mes == 0) ? 12 : $mes;
			$data = (object)array("mes" => $mes, "cantidad" => 0);
			$afiliacionesPorMes[] = $data;
		}
		$capm = count($afiliacionesPorMes);
		for($i = 0; $i < $capm; $i++) {
			if($i < 12){
				$mes = "";
				switch ($afiliacionesPorMes[$i]->mes) {
					case '1': $mes = 'ENE'; break;
					case '2': $mes = 'FEB'; break;
					case '3': $mes = 'MAR'; break;
					case '4': $mes = 'ABR'; break;
					case '5': $mes = 'MAY'; break;
					case '6': $mes = 'JUN'; break;
					case '7': $mes = 'JUL'; break;
					case '8': $mes = 'AGO'; break;
					case '9': $mes = 'SEP'; break;
					case '10': $mes = 'OCT'; break;
					case '11': $mes = 'NOV'; break;
					case '12': $mes = 'DIC'; break;
				}
				$afiliacionesPorMes[$i]->mes = $mes;
				$afiliacionesPorMes[$i]->anterior = $afiliacionesPorMes[$i + 12]->cantidad;
			}
			else{
				array_pop($afiliacionesPorMes);
			}
		}

		foreach ($retirosDelMes as &$item) {
			try{
				$diff = substr($item->fecha_retiro, 0, 10);
				$item->fecha_retiro = Carbon::createFromFormat('d/m/Y', $diff)->startOfDay();
				$item->total = $item->ahorros + $item->cartera;
			}
			catch(\InvalidArgumentException $e){
			}
		}
		$afiliacionesPorMes = array_reverse($afiliacionesPorMes);

		$vista = view("reportes.estadisticos.asociados")
			->withFechaMostrar($this->getFechaParaMostrar($fecha))
			->withEntidad($this->getEntidad())
			->withAsociadosPorGenero($asociadosPorGenero)
			->withAsociadosPorPagaduria($asociadosPorPagaduria)
			->withAsociadosPorAntiguedad($asociadosPorAntiguedad)
			->withAsociadosPorEdad($asociadosPorEdad)
			->withAsociadosSinCreditos($asociadosSinCreditos)
			->withAsociadosDelMes($asociadosDelMes)
			->withRetirosDelMes($retirosDelMes)
			->withAfiliacionesPorMes($afiliacionesPorMes)
			->withFecha($fecha)
			->render();

		return $vista;
	}

	/**
	 * Reportes estadísticos de ahorros
	 * @return type
	 */
	public function estadisticoAhorros($fecha) {
		$entidad = $this->getEntidad();

		//Asociados por genero
		$sql = "SELECT moda.codigo AS codigo, moda.nombre AS nombre, SUM(ma.valor_movimiento) AS valor FROM ahorros.movimientos_ahorros AS ma INNER JOIN ahorros.modalidades_ahorros AS moda ON ma.modalidad_ahorro_id = moda.id WHERE ma.entidad_id = ? AND general.fn_fecha_sin_hora(ma.fecha_movimiento) <= ? GROUP BY moda.codigo, moda.nombre HAVING SUM(ma.valor_movimiento) <> 0 UNION SELECT tsd.codigo AS codigo, tsd.nombre AS nombre, SUM(msd.valor) AS valor FROM ahorros.tipos_sdat AS tsd INNER JOIN ahorros.sdats AS sds ON sds.tipo_sdat_id=tsd.id INNER JOIN ahorros.movimientos_sdat AS msd ON msd.sdat_id=sds.id WHERE tsd.entidad_id = ? AND general.fn_fecha_sin_hora(msd.fecha_movimiento) <= ? GROUP BY tsd.codigo, tsd.nombre HAVING SUM(msd.valor) <> 0 ORDER BY valor DESC";
		$saldosPorModalidad = DB::select($sql, [$entidad->id, $fecha, $entidad->id, $fecha]);
		$total = 0;
		foreach ($saldosPorModalidad as $saldo) $total += $saldo->valor;
		if($total > 0) {
			foreach ($saldosPorModalidad as $saldo) {
				$saldo->participacion = ($saldo->valor * 100) / $total;
			}
		}

		//Ahorros posr rangos
		$sql = "SELECT t.nombre, moda.codigo, moda.nombre AS modalidad, SUM(ma.valor_movimiento) AS valor FROM ahorros.movimientos_ahorros AS ma INNER JOIN ahorros.modalidades_ahorros AS moda ON ma.modalidad_ahorro_id = moda.id INNER JOIN socios.socios AS so ON ma.socio_id = so.id INNER JOIN general.terceros AS t ON so.tercero_id = t.id WHERE ma.entidad_id = ? AND general.fn_fecha_sin_hora(ma.fecha_movimiento) <= ? GROUP BY t.nombre, moda.codigo, moda.nombre HAVING SUM(ma.valor_movimiento) > 0 ORDER BY moda.nombre ASC, SUM(ma.valor_movimiento) DESC";
		$res = DB::select($sql, [$entidad->id, $fecha]);
		$res = collect($res);
		$rangosDeSaldos = $res->groupBy("nombre")->transform(function($item, $key){
			return $item->sum('valor');
		});
		$total = $rangosDeSaldos->count();
		$rangos = collect();
		$rangos->put(0, (object)array("nombre" => "Mayor a 5 millones", "cantidad" => 0, "porcentaje" => 0));
		$rangos->put(1, (object)array("nombre" => "Entre 2 y 5 millones", "cantidad" => 0, "porcentaje" => 0));
		$rangos->put(2, (object)array("nombre" => "Entre 1 y 2 millones", "cantidad" => 0, "porcentaje" => 0));
		$rangos->put(3, (object)array("nombre" => "Entre 500 mil y 1 millón", "cantidad" => 0, "porcentaje" => 0));
		$rangos->put(4, (object)array("nombre" => "Hasta 500 mil", "cantidad" => 0, "porcentaje" => 0));
		foreach ($rangosDeSaldos as $value) {
			if($value >= 5000000) {
				$rangos[0]->cantidad++;
			}
			elseif($value >= 2000000 && $value < 5000000) {
				$rangos[1]->cantidad++;
			}
			elseif($value >= 1000000 && $value < 2000000) {
				$rangos[2]->cantidad++;
			}
			elseif($value >= 500000 && $value < 1000000) {
				$rangos[3]->cantidad++;
			}
			else {
				$rangos[4]->cantidad++;
			}
		}
		foreach($rangos as $rango) {
			$rango->porcentaje = round(($rango->cantidad * 100) / $total, 2);
		}

		//Saldos por empresa
		$sql = "SELECT p.nombre, SUM(ma.valor_movimiento) AS valor FROM ahorros.movimientos_ahorros AS ma INNER JOIN ahorros.modalidades_ahorros AS moda ON ma.modalidad_ahorro_id = moda.id INNER JOIN socios.socios AS so ON ma.socio_id = so.id INNER JOIN recaudos.pagadurias AS p ON so.pagaduria_id = p.id WHERE ma.entidad_id = ? AND general.fn_fecha_sin_hora(ma.fecha_movimiento) <= ? GROUP BY p.nombre HAVING SUM(ma.valor_movimiento) > 0 ORDER BY SUM(ma.valor_movimiento) DESC";
		$saldosPorEmpresa = DB::select($sql, [$entidad->id, $fecha]);
		$total = 0;
		foreach($saldosPorEmpresa as $saldo) $total += $saldo->valor;
		foreach($saldosPorEmpresa as $saldo) {
			$saldo->porcentaje = ($saldo->valor * 100) / $total;
		}

		$consolidado = $res->groupBy('nombre')->transform(function($item, $key){
			return $item->sum('valor');
		});
		$consolidado = $consolidado->sort()->reverse()->take(10);
		$topDiezPorModalidad = $res->groupBy("modalidad");

		$sql = "exec ahorros.sp_comparativo_ahorros ?, ?";
		$comparativoAhorros = DB::select($sql, [$entidad->id, $fecha]);

		$sql = "ahorros.sp_variacion_ahorros ?, ?";
		$variacionAhorros = DB::select($sql, [$entidad->id, $fecha]);
		$vista = view("reportes.estadisticos.ahorros")
			->withFechaMostrar($this->getFechaParaMostrar($fecha))
			->withEntidad($this->getEntidad())
			->withSaldosPorModalidad($saldosPorModalidad)
			->withRangosDeSaldos($rangos)
			->withSaldosPorEmpresa($saldosPorEmpresa)
			->withTopDiezPorModalidad($topDiezPorModalidad)
			->withTotalConsolidados($consolidado)
			->withComparativoAhorros($comparativoAhorros)
			->withVariacionAhorros($variacionAhorros)
			->withFecha($fecha)
			->render();

		return $vista;
	}

	/**
	 * Reportes estadísticos de cartera
	 * @return type
	 */
	public function estadisticoCartera($fecha) {
		$entidad = $this->getEntidad();

		$sql = "SELECT t.numero_identificacion, t.nombre, p.nombre AS pagaduria, mcc.solicitud_credito_id, m.nombre AS modalidad, sc.tasa, SUM(mcc.valor_movimiento) AS saldo, sc.calificacion_obligacion FROM creditos.movimientos_capital_credito AS mcc INNER JOIN creditos.solicitudes_creditos AS sc ON mcc.solicitud_credito_id = sc.id INNER JOIN creditos.modalidades AS m ON sc.modalidad_credito_id = m.id INNER JOIN general.terceros AS t ON sc.tercero_id = t.id LEFT JOIN socios.socios AS s ON s.tercero_id = t.id LEFT JOIN recaudos.pagadurias AS p ON s.pagaduria_id = p.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(mcc.fecha_movimiento) <= ? GROUP BY t.numero_identificacion, t.nombre, p.nombre, mcc.solicitud_credito_id, m.nombre, sc.tasa, sc.calificacion_obligacion HAVING SUM(mcc.valor_movimiento) > 0 ORDER BY saldo DESC";
		$cartera = collect(DB::select($sql, [$entidad->id, $fecha]));

		$sql = "SELECT t.nombre, SUM(ma.valor_movimiento) ahorros FROM ahorros.movimientos_ahorros AS ma INNER JOIN socios.socios AS s ON ma.socio_id = s.id INNER JOIN general.terceros AS t ON s.tercero_id = t.id WHERE t.entidad_id = ? AND general.fn_fecha_sin_hora(ma.fecha_movimiento) <= ? GROUP BY t.nombre";
		$ahorros = collect(DB::select($sql, [$entidad->id, $fecha]));

		$carteraPorModalidad = collect();
		$tmp = $cartera->groupBy("modalidad");
		foreach($tmp as $key => $value) {
			$cantidad = $value->count();
			$saldo = $value->sum('saldo');
			$tasa = $value->sum('tasa');
			$obj = (object)array(
						'modalidad' => $key,
						'cantidadCreditos' => $cantidad,
						'participacion' => ($cantidad * 100) / $cartera->count(),
						'saldo' => $saldo,
						'participacionSaldo' => ($saldo * 100) / $cartera->sum('saldo'),
						'tasaPromedio' => $tasa / $cantidad
			);
			$carteraPorModalidad->push($obj);
		}

		$distribucionPorCalificacion = collect();
		$tmp = $cartera->groupBy("calificacion_obligacion");
		foreach($tmp as $key => $value) {
			$cantidad = $value->count();
			$saldo = $value->sum('saldo');
			$tasa = $value->sum('tasa');
			$obj = (object)array(
						'calificacion' => $key,
						'cantidadCreditos' => $cantidad,
						'participacion' => ($cantidad * 100) / $cartera->count(),
						'saldo' => $saldo,
						'participacionSaldo' => ($saldo * 100) / $cartera->sum('saldo')
			);
			$distribucionPorCalificacion->push($obj);
		}

		$distribucionPorEmpresa = collect();
		$tmp = $cartera->groupBy("pagaduria");
		foreach($tmp as $key => $value) {
			$cantidad = $value->count();
			$saldo = $value->sum('saldo');
			$tasa = $value->sum('tasa');
			$obj = (object)array(
						'empresa' => $key,
						'cantidadCreditos' => $cantidad,
						'participacion' => ($cantidad * 100) / $cartera->count(),
						'saldo' => $saldo,
						'participacionSaldo' => ($saldo * 100) / $cartera->sum('saldo')
			);
			$distribucionPorEmpresa->push($obj);
		}

		//TOP QUINCE
		$topQuince = collect();
		$tmp = $cartera->groupBy("nombre")->transform(function ($items, $key) {
			return $items->sum('saldo');
		})->sort()->reverse();

		$data = collect();
		$total = $cartera->sum("saldo");
		$tAhorro = 0;
		$tCartera = 0;
		$tParticiopacionModalidad = 0;
		$tParticipacionTotalCartera = 0;
		foreach ($tmp->take(15) as $key => $value) {
			$ahorro = $ahorros->where("nombre", $key)->first();
			$ahorro = is_null($ahorro) ? 0 : floatval($ahorro->ahorros);
			$tAhorro += $ahorro;
			$tCartera += $value;
			$tParticiopacionModalidad += ($value * 100) / $total;
			$tParticipacionTotalCartera += ($value * 100) / $total;
			$obj = (object)array(
				"nombre" => $key,
				"ahorros" => $ahorro,
				"value" => $value,
				"participacionModalidad" => ($value * 100) / $total,
				"participacionTotalCartera" => ($value * 100) / $total
			);
			$data->push($obj);
		}
		$totalAhorros = 0;
		foreach ($tmp as $key => $value) {
			$ahorro = $ahorros->where("nombre", $key)->first();
			$totalAhorros += is_null($ahorro) ? 0 : floatval($ahorro->ahorros);
		}
		$obj = (object)array(
			"DATA" => $data,
			"ahorros" => $tAhorro,
			"cartera" => $tCartera,
			"participacionModalidad" => $tParticiopacionModalidad,
			"participacionTotalCartera" => $tParticipacionTotalCartera,
			"totalAhorros" => $totalAhorros,
			"totalCartera" => $total,
			"totalModalidadAhorro" => 0,
			"totalModalidadCartera" => 0,
			"totalParticipacionCartera" => 0
		);
		$topQuince->push($obj);

		$tmpModalidad = $cartera->groupBy("modalidad");
		foreach ($tmpModalidad as $key => $value) {
			$tmp = $value->groupBy("nombre")->transform(function ($items, $key) {
				return $items->sum('saldo');
			})->sort()->reverse();
			$data = collect();
			$subTotal = $tmp->sum();
			$tAhorro = 0;
			$tCartera = 0;
			$tParticiopacionModalidad = 0;
			$tParticipacionTotalCartera = 0;
			foreach ($tmp->take(15) as $key => $value) {
				$ahorro = $ahorros->where("nombre", $key)->first();
				$ahorro = is_null($ahorro) ? 0 : floatval($ahorro->ahorros);
				$tAhorro += $ahorro;
				$tCartera += $value;
				$tParticiopacionModalidad += ($value * 100) / $subTotal;
				$tParticipacionTotalCartera += ($value * 100) / $total;
				$obj = (object)array(
					"nombre" => $key,
					"ahorros" => $ahorro,
					"value" => $value,
					"participacionModalidad" => ($value * 100) / $subTotal,
					"participacionTotalCartera" => ($value * 100) / $total
				);
				$data->push($obj);
			}
			$subTotalAhorros = 0;
			foreach ($tmp as $key => $value) {
				$ahorro = $ahorros->where("nombre", $key)->first();
				$subTotalAhorros += is_null($ahorro) ? 0 : floatval($ahorro->ahorros);
			}
			$obj = (object)array(
				"DATA" => $data,
				"ahorros" => $tAhorro,
				"cartera" => $tCartera,
				"participacionModalidad" => $tParticiopacionModalidad,
				"participacionTotalCartera" => $tParticipacionTotalCartera,
				"totalModalidadAhorro" => $subTotalAhorros,
				"totalModalidadCartera" => $subTotal,
				"totalAhorros" => $totalAhorros,
				"totalCartera" => $total,
				"totalParticipacionCartera" => ($subTotal * 100) / $total
			);
			$topQuince->push($obj);
		}

		$sql = "exec creditos.sp_comparativo_creditos ?, ?";
		$comparativoCartera = DB::select($sql, [$entidad->id, $fecha]);

		$sql = "WITH consolidado AS( SELECT oc.solicitud_credito_id, SUM(COALESCE(oc.pago_capital, 0) + COALESCE(oc.pago_intereses, 0)) consolidado FROM creditos.obligaciones_consolidacion AS oc INNER JOIN creditos.solicitudes_creditos AS sc ON oc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? AND oc.deleted_at IS NULL GROUP BY oc.solicitud_credito_id ) SELECT m.nombre, COUNT(sc.id) cantidad, SUM(sc.valor_credito) monto, SUM(COALESCE(oc.consolidado, 0)) consolidado, SUM(sc.valor_credito) - SUM(COALESCE(oc.consolidado, 0)) neto FROM creditos.solicitudes_creditos AS sc INNER JOIN creditos.modalidades AS m ON sc.modalidad_credito_id = m.id LEFT JOIN consolidado AS oc ON oc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(sc.fecha_desembolso) BETWEEN ? AND ? AND sc.estado_solicitud IN ('DESEMBOLSADO', 'SALDADO') GROUP BY m.nombre ORDER BY neto DESC";
		$colocacionesPorModalidad = DB::select($sql, [$entidad->id, $entidad->id, $fecha->copy()->startOfMonth(), $fecha]);

		$sql = "WITH consolidado AS( SELECT oc.solicitud_credito_id, SUM(COALESCE(oc.pago_capital, 0) + COALESCE(oc.pago_intereses, 0)) consolidado FROM creditos.obligaciones_consolidacion AS oc INNER JOIN creditos.solicitudes_creditos AS sc ON oc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? AND oc.deleted_at IS NULL GROUP BY oc.solicitud_credito_id ) SELECT COALESCE(p.nombre, '') empresa, COUNT(sc.id) cantidad, SUM(sc.valor_credito) monto, SUM(COALESCE(oc.consolidado, 0)) consolidado, SUM(sc.valor_credito) - SUM(COALESCE(oc.consolidado, 0)) neto FROM creditos.solicitudes_creditos AS sc INNER JOIN general.terceros AS t ON sc.tercero_id = t.id LEFT JOIN socios.socios AS s ON s.tercero_id = t.id LEFT JOIN recaudos.pagadurias AS p on s.pagaduria_id = p.id LEFT JOIN consolidado AS oc ON oc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(sc.fecha_desembolso) BETWEEN ? AND ? AND sc.estado_solicitud IN ('DESEMBOLSADO', 'SALDADO') GROUP BY p.nombre ORDER BY neto DESC";
		$colocacionesPorEmpresa = DB::select($sql, [$entidad->id, $entidad->id, $fecha->copy()->startOfMonth(), $fecha]);

		$sql = "exec creditos.sp_comparativo_colocacion_mes ?, ?";
		$comparativoColocaciones = DB::select($sql, [$entidad->id, $fecha]);

		$consolidadoColocacionesAnio = DB::select($sql, [$entidad->id, $fecha]);
		for($i = count($consolidadoColocacionesAnio); $i > $fecha->month; $i--) {
			array_shift($consolidadoColocacionesAnio);
		}
		for($acumuladoAnterior = 0, $acumuladoActual = 0, $i = 0; $i < count($consolidadoColocacionesAnio); $i++){
			$consolidadoColocacionesAnio[$i]->anterior += $acumuladoAnterior;
			$consolidadoColocacionesAnio[$i]->actual += $acumuladoActual;
			$acumuladoAnterior = $consolidadoColocacionesAnio[$i]->anterior;
			$acumuladoActual = $consolidadoColocacionesAnio[$i]->actual;
		}

		$vista = view("reportes.estadisticos.cartera")
			->withFechaMostrar($this->getFechaParaMostrar($fecha))
			->withEntidad($this->getEntidad())
			->withCarteraPorModalidad($carteraPorModalidad->sortByDesc('saldo'))
			->withDistribucionPorCalificacion($distribucionPorCalificacion->sortBy('calificacion'))
			->withDistribucionPorEmpresa($distribucionPorEmpresa->sortByDesc('saldo'))
			->withTopQuince($topQuince)
			->withModalidades($carteraPorModalidad->groupBy("modalidad")->keys()->toArray())
			->withComparativoCartera($comparativoCartera)
			->withFecha($fecha)
			->withColocacionesPorModalidad($colocacionesPorModalidad)
			->withColocacionesPorEmpresa($colocacionesPorEmpresa)
			->withComparativoColocaciones($comparativoColocaciones)
			->withConsolidadoColocacionesAnio($consolidadoColocacionesAnio)
			->render();

		return $vista;
	}

	////////////////////////////////////////////////////////////////////////////

	public function getReporte(Reporte $obj, Request $request) {
		try {
			$funcionReporte = $obj->ruta_reporte;
			if(!method_exists($this, $funcionReporte)) {
				throw new \BadMethodCallException("prueba");
			}
			$data = $this->$funcionReporte($request);
			if($request->print) {
				return view("reportes.print")
						->withData($data);
			}
			else {
				return view("reportes.reporte")
						->withReporte($obj)
						->withData($data);
			}
		}
		catch(\BadMethodCallException $e) {
			Session::flash("error", "No se encontró el reporte");
			return redirect('reportes');
		}
	}

	/*INICIO CONTABILIDAD*/

	/**
	 * Reporte de contabilidad comprobante contable
	 * @param type $request
	 * @return type
	 */
	public function contabilidadComprobanteContable($request) {
		$codigoComprobante = $request->codigoComprobante;
		$numeroComprobante = $request->numeroComprobante;

		$query = "SELECT mv.id, mv.fecha_movimiento AS FechaMovimiento, tc.nombre AS TipoDeComprobante, tc.codigo AS CodigoComprobante, mv.numero_comprobante AS NumeroComprobante, mv.descripcion AS Descripcion FROM contabilidad.movimientos AS mv INNER JOIN contabilidad.tipos_comprobantes AS tc ON mv.tipo_comprobante_id = tc.id WHERE mv.entidad_id = ? AND tc.codigo = ? AND mv.numero_comprobante = ?";
		$DSCabecera = DB::select($query, [$this->getEntidad()->id, $codigoComprobante, $numeroComprobante]);

		if(!$DSCabecera)return "";
		$DSCabecera = $DSCabecera[0];
		$DSCabecera->FechaMovimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $DSCabecera->FechaMovimiento);
		$query = "SELECT dm.cuif_codigo AS cuenta, dm.cuif_nombre AS nombre, CONCAT(dm.tercero_identificacion, ' - ', t.nombre) AS nombreTercero, dm.referencia AS referencia, dm.debito AS debitos, dm.credito AS creditos FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.movimientos AS mv ON dm.movimiento_id = mv.id INNER JOIN contabilidad.tipos_comprobantes AS tc ON mv.tipo_comprobante_id = tc.id INNER JOIN general.terceros AS t ON dm.tercero_id = t.id WHERE mv.entidad_id = ? AND tc.codigo = ? AND mv.numero_comprobante = ?";
		$DSDetalle = DB::select($query, [$this->getEntidad()->id, $codigoComprobante, $numeroComprobante]);
		if(!$DSDetalle)return "";

		$mv = Movimiento::find($DSCabecera->id);
		$causaAnulacion = optional($mv)->causaAnulacionMovimiento;

		return view("reportes.contabilidad.comprobanteContable")
					->withEntidad($this->getEntidad())
					->withCabecera($DSCabecera)
					->withDetalles($DSDetalle)
					->withCausaAnulacion($causaAnulacion)
					->render();
	}

	/*AUXILIAR CONTABLE POR CUENTA*/
	public function contabilidadAuxiliarPorCuenta($request) {
		$validate = Validator::make($request->all(), [
			'fechaInferior'		=> 'bail|required|date_format:"Y/m/d"',
			'fechaSuperior'		=> 'bail|required|date_format:"Y/m/d"',
			'cuentaContable'	=> 'bail|required|string|min:1|max:10',
		]);

		if($validate->fails())return "";
		$fechaInferior = Carbon::createFromFormat('Y/m/d', $request->fechaInferior)->startOfDay();
		$fechaSuperior = Carbon::createFromFormat('Y/m/d', $request->fechaSuperior)->startOfDay();
		$cuentaContable = $request->cuentaContable;

		$entidad = $this->getEntidad();

		$querySaldoAnterior = "SELECT CASE WHEN c.naturaleza = 'DÉBITO' THEN COALESCE(SUM(dm.debito), 0) - COALESCE(SUM(dm.credito), 0) ELSE COALESCE(SUM(dm.credito), 0) - COALESCE(SUM(dm.debito), 0) END Saldo FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.cuifs AS c on dm.cuif_id = c.id INNER JOIN contabilidad.movimientos AS m ON dm.movimiento_id = m.id WHERE dm.entidad_id = ? AND dm.cuif_codigo LIKE ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) < ? AND m.causa_anulado_id IS NULL GROUP BY c.naturaleza";
		$saldo = DB::select($querySaldoAnterior, [$entidad->id, $cuentaContable, $fechaInferior]);
		$saldo = empty($saldo) ? 0 : $saldo[0]->Saldo;

		$query = "SELECT dm.fecha_movimiento AS Fecha, dm.codigo_comprobante AS Comprobante, dm.numero_comprobante AS Numero, dm.tercero_identificacion AS Identificacion, dm.tercero AS Nombre, dm.cuif_codigo AS Cuenta, dm.cuif_nombre AS NombreCuenta, m.descripcion AS Descripcion, dm.debito AS Debito, dm.credito AS Credito, dm.referencia AS Referencia, c.naturaleza AS Naturaleza FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.movimientos AS m ON dm.movimiento_id = m.id INNER JOIN contabilidad.cuifs AS c on dm.cuif_id = c.id WHERE dm.entidad_id = ? AND dm.cuif_codigo LIKE ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) BETWEEN ? AND ? AND m.causa_anulado_id IS NULL ORDER BY dm.fecha_movimiento ASC";
		$DSMovimientos = DB::select($query, [$entidad->id, $cuentaContable, $fechaInferior, $fechaSuperior]);

		if(!$DSMovimientos)return "";

		$saldoAnterior = $saldo;
		foreach($DSMovimientos as &$movimientos) {
			$movimientos->Fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $movimientos->Fecha);
			$movimientos->saldo = $saldoAnterior + ($movimientos->Naturaleza == 'DÉBITO' ? $movimientos->Debito - $movimientos->Credito : $movimientos->Credito - $movimientos->Debito);
			$saldoAnterior = $movimientos->saldo;
		}

		return view("reportes.contabilidad.auxiliarContable")
					->withEntidad($entidad)
					->withMovimientos($DSMovimientos)
					->withFechaInferior($fechaInferior)
					->withFechaSuperior($fechaSuperior)
					->withSaldo($saldo)
					->render();
	}

	/**
	 * Ejecuta el balance de prueba
	 * @param type $request
	 * @return type
	 */
	public function contabilidadBalancePrueba($request) {
		$validate = Validator::make($request->all(), [
			'anio'		=> 'bail|required|integer|digits:4|min:2000|max:3000',
			'mes'		=> 'bail|required|digits_between:1,2|min:1|max:12',
			'nivel'		=> 'bail|required|integer|digits:1|min:1|max:5'
		]);

		if($validate->fails())return "";

		$mes = intval($request->mes);
		if($mes < 1 || $mes > 12)return "";

		$entidad = $this->getEntidad();

		$DSMovimientos = DB::select('exec contabilidad.sp_balance_prueba ?, ?, ?, ?', [$entidad->id, $request->anio, $mes, 5]);

		$resultados = array(
			'saldoAnteriorDebito' => 0,
			'saldoAnteriorCredito' => 0,
			'debitos' => 0,
			'creditos' => 0,
			'saldoDebito' => 0,
			'saldoCredito' => 0
		);

		if(!$DSMovimientos)return "";
		if(!empty($DSMovimientos[0]->ERROR)) {
			//Session::flash('error', $DSMovimientos[0]->MENSAJE);
			return "";
		}

		foreach($DSMovimientos as $key => $movimiento) {
			if($movimiento->nivel == 5) {
				if($movimiento->naturaleza == 'DÉBITO') {
					$resultados['saldoAnteriorDebito'] += $movimiento->saldo_anterior;
					$resultados['saldoDebito'] += $movimiento->saldo;
				}
				else {
					$resultados['saldoAnteriorCredito'] += $movimiento->saldo_anterior;
					$resultados['saldoCredito'] += $movimiento->saldo;
				}
				$resultados['debitos'] += $movimiento->debitos;
				$resultados['creditos'] += $movimiento->creditos;
			}
			if($movimiento->nivel > $request->nivel)unset($DSMovimientos[$key]);
		}

		$mes = "";
		switch ($request->mes) {
			case 1: $mes = 'Enero'; break;
			case 2: $mes = 'Febrero'; break;
			case 3: $mes = 'Marzo'; break;
			case 4: $mes = 'Abril'; break;
			case 5: $mes = 'Mayo'; break;
			case 6: $mes = 'Junio'; break;
			case 7: $mes = 'Julio'; break;
			case 8: $mes = 'Agosto'; break;
			case 9: $mes = 'Septiembre'; break;
			case 10: $mes = 'Octubre'; break;
			case 11: $mes = 'Noviembre'; break;
			case 12: $mes = 'Diciembre'; break;
			default: break;
		}

		return view("reportes.contabilidad.balancePrueba")
					->withEntidad($entidad)
					->withMovimientos($DSMovimientos)
					->withAnio($request->anio)
					->withMes($mes)
					->withResultados($resultados)
					->render();
	}

	/**
	 * SALDOS DE CUENTA POR TERCERO
	 * @param type $request
	 * @return type
	 */
	public function contabilidadSaldosCuentaTercero($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
			'cuentaContable'	=> 'bail|required|string|min:1|max:10',
		]);
		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$cuentaContable = $request->cuentaContable;

		$entidad = $this->getEntidad();

		$query = "WITH datos AS ( SELECT dm.tercero_id, CONVERT(DECIMAL (15,2), 0) AS saldo_anterior, CONVERT(DECIMAL (15,2), 0) AS debitos, CONVERT(DECIMAL (15,2), 0) AS creditos, CASE WHEN c.naturaleza = 'DEBITO' THEN SUM(dm.debito) - SUM(dm.credito) ELSE SUM(dm.credito) - SUM(dm.debito) END AS saldo_nuevo FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.cuifs AS c ON dm.cuif_id = c.id INNER JOIN contabilidad.movimientos AS mv ON DM.movimiento_id = mv.id WHERE mv.causa_anulado_id IS NULL AND dm.entidad_id = ? AND c.codigo = ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) <= ? GROUP BY dm.tercero_id, c.naturaleza HAVING SUM(dm.credito) - SUM(dm.debito) <> 0 UNION ALL SELECT dm.tercero_id, CASE WHEN c.naturaleza = 'DEBITO' THEN SUM(dm.debito) - SUM(dm.credito) ELSE SUM(dm.credito) - SUM(dm.debito) END, 0, 0, 0 FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.cuifs AS c ON dm.cuif_id = c.id INNER JOIN contabilidad.movimientos AS mv ON DM.movimiento_id = mv.id WHERE mv.causa_anulado_id IS NULL AND dm.entidad_id = ? AND c.codigo = ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) <= EOMONTH(DATEADD(MONTH, -1, ?)) GROUP BY dm.tercero_id, c.naturaleza HAVING SUM(dm.credito) - SUM(dm.debito) <> 0 UNION ALL SELECT dm.tercero_id, 0, SUM(dm.debito), SUM(dm.credito), 0 FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.cuifs AS c ON dm.cuif_id = c.id INNER JOIN contabilidad.movimientos AS mv ON DM.movimiento_id = mv.id WHERE mv.causa_anulado_id IS NULL AND dm.entidad_id = ? AND c.codigo = ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) BETWEEN DATEADD(DAY, -(DAY(?) -1), ?) AND ? GROUP BY dm.tercero_id HAVING SUM(dm.credito) - SUM(dm.debito) <> 0 ) SELECT t.numero_identificacion AS identificacion, t.nombre, SUM(saldo_anterior) AS saldo_anterior, SUM(debitos) AS debitos, SUM(creditos) AS creditos, SUM(saldo_nuevo) AS saldo_nuevo FROM datos AS d INNER JOIN general.terceros AS t ON d.tercero_id = t.id GROUP BY t.numero_identificacion, t.nombre ORDER BY nombre";
		$DSSaldosCuenta = DB::select($query, [$entidad->id, $cuentaContable, $fechaCorte, $entidad->id, $cuentaContable, $fechaCorte, $entidad->id, $cuentaContable, $fechaCorte, $fechaCorte, $fechaCorte]);

		if(!$DSSaldosCuenta)return "";
		return view("reportes.contabilidad.saldosCuentaTercero")
					->withEntidad($entidad)
					->withSaldos($DSSaldosCuenta)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * MOVIMIENTO CONTABLE POR TERCERO
	 * @param type $request
	 * @return type
	 */
	public function movimientosTercero($request) {
		$validate = Validator::make($request->all(), [
			'fechaInicio'			=> 'bail|required|date_format:"Y/m/d"',
			'fechaFin'				=> 'bail|required|date_format:"Y/m/d"',
			'terceroIdentificacion'	=> 'bail|required|string|min:1|max:15',
			'cuentaContable'		=> 'bail|required|string|min:1|max:10',
		]);
		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFin)->startOfDay();
		$terceroIdentificacion = $request->terceroIdentificacion;
		$cuentaContable = $request->cuentaContable;

		$entidad = $this->getEntidad();

		$query = "SELECT dm.fecha_movimiento AS fecha, dm.codigo_comprobante AS comprobante, dm.numero_comprobante AS numero, t.numero_identificacion AS identificacion, t.nombre AS nombre, c.codigo AS cuenta, c.nombre AS nombrecuenta, dm.debito AS debito, dm.credito AS credito, dm.referencia AS referencia FROM contabilidad.detalle_movimientos AS dm INNER JOIN general.terceros AS t ON dm.tercero_id = t.id INNER JOIN contabilidad.cuifs AS c ON dm.cuif_id = c.id INNER JOIN contabilidad.movimientos AS mv ON dm.movimiento_id = mv.id WHERE mv.causa_anulado_id IS NULL AND t.entidad_id = ? AND t.numero_identificacion = ? AND dm.cuif_codigo LIKE ? AND general.fn_fecha_sin_hora(dm.fecha_movimiento) BETWEEN ? AND ? ORDER BY c.codigo, dm.fecha_movimiento, dm.referencia";
		$DSMovimientosTercero = DB::select($query, [$entidad->id, $terceroIdentificacion, $cuentaContable, $fechaInicio, $fechaFin]);
		if(!$DSMovimientosTercero)return "";

		foreach($DSMovimientosTercero as &$movimientos) {
			$movimientos->fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $movimientos->fecha);
		}
		return view("reportes.contabilidad.movimientosTercero")
					->withEntidad($entidad)
					->withMovimientos($DSMovimientosTercero)
					->withFechaInicio($fechaInicio)
					->withFechaFin($fechaFin)
					->render();
	}

	/**
	 * CONTABILIDAD INFORMACION TRIBUTARIA
	 * @param type $request
	 * @return type
	 */
	public function contabilidadInformacionTributaria($request) {
		$e = $this->getEntidad();
		$validate = Validator::make($request->all(), [
			'fechaInicio' => 'bail|required|date_format:"Y/m/d"',
			'fechaFinal' => 'bail|required|date_format:"Y/m/d"',
			'impuesto' => [
				'bail',
				'required',
				'integer',
				"exists:sqlsrv.contabilidad.impuestos,id,entidad_id,$e->id,esta_activo,1,deleted_at,NULL"
			]
		]);
		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFinal)->startOfDay();
		$i = Impuesto::find($request->impuesto);

		$query = "SELECT cp.nombre, CONVERT(DECIMAL(15,0), ROUND(SUM(mi.base) / 1000, 0) * 1000) AS base, CONVERT(DECIMAL(15,0), ROUND(SUM(mi.valor_impuesto) / 1000, 0) * 1000) AS impuesto FROM contabilidad.movimiento_impuesto AS mi INNER JOIN contabilidad.conceptos_impuestos AS cp ON mi.concepto_impuesto_id = cp.id WHERE mi.entidad_id = ? AND general.fn_fecha_sin_hora(mi.fecha_movimiento) BETWEEN ? AND ? AND mi.impuesto_id = ? GROUP BY cp.nombre";
		$DSInformacionTributaria = DB::select($query, [$e->id, $fechaInicio, $fechaFin, $request->impuesto]);
		if(!$DSInformacionTributaria)return "";

		return view("reportes.contabilidad.informacionTributaria")
					->withEntidad($e)
					->withInformacionesTributarias($DSInformacionTributaria)
					->withFechaInicio($fechaInicio)
					->withFechaFinal($fechaFin)
					->withImpuesto($i)
					->render();
	}

	/**
	 * CONTABILIDAD DETALLE INFORMACION TRIBUTARIA
	 * @param type $request
	 * @return type
	 */
	public function contabilidadDetalleInformacionTributaria($request) {
		$e = $this->getEntidad();
		$validate = Validator::make($request->all(), [
			'fechaInicio' => 'bail|required|date_format:"Y/m/d"',
			'fechaFinal' => 'bail|required|date_format:"Y/m/d"',
			'impuesto' => [
				'bail',
				'required',
				'integer',
				"exists:sqlsrv.contabilidad.impuestos,id,entidad_id,$e->id,esta_activo,1,deleted_at,NULL"
			]
		]);
		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFinal)->startOfDay();
		$i = Impuesto::find($request->impuesto);

		$query = "SELECT mi.fecha_movimiento, tc.codigo, m.numero_comprobante, mi.tercero_identificacion, mi.tercero, i.nombre AS impuesto, cp.nombre, mi.cuif_codigo, mi.tasa, mi.base, mi.valor_impuesto FROM contabilidad.movimiento_impuesto AS mi INNER JOIN contabilidad.conceptos_impuestos AS cp ON mi.concepto_impuesto_id = cp.id INNER JOIN contabilidad.movimientos AS m ON mi.movimiento_id = m.id INNER JOIN contabilidad.tipos_comprobantes AS tc ON m.tipo_comprobante_id = tc.id INNER JOIN contabilidad.impuestos AS i ON mi.impuesto_id = i.id WHERE mi.entidad_id = ? AND general.fn_fecha_sin_hora(mi.fecha_movimiento) BETWEEN ? AND ? AND mi.impuesto_id = ? ORDER BY nombre";
		$DSDetalle = DB::select($query, [$e->id, $fechaInicio, $fechaFin, $request->impuesto]);
		if(!$DSDetalle)return "";

		foreach($DSDetalle as &$movimientos) {
			$movimientos->fecha_movimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $movimientos->fecha_movimiento);
		}

		return view("reportes.contabilidad.detalleInformacionTributaria")
					->withEntidad($e)
					->withDetalle($DSDetalle)
					->withFechaInicio($fechaInicio)
					->withFechaFinal($fechaFin)
					->withImpuesto($i)
					->render();
	}

	/*FIN CONTABILIDAD*/

	/*INICIO AHORROS*/
	/*SALDOS AHORROS POR MODALIDAD*/
	public function ahorrosSaldosModalidad($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS Identificacion,t.nombre AS Nombre, md.nombre AS Modalidad, SUM(ma.valor_movimiento) AS Saldo FROM ahorros.movimientos_ahorros AS ma INNER JOIN socios.socios AS s	ON ma.socio_id=s.id INNER JOIN general.terceros AS t ON s.tercero_id=t.id INNER JOIN ahorros.modalidades_ahorros AS md ON ma.modalidad_ahorro_id=md.id WHERE t.entidad_id = ? AND general.fn_fecha_sin_hora(ma.fecha_movimiento) <= ? GROUP BY t.numero_identificacion, t.nombre, md.nombre HAVING SUM(ma.valor_movimiento) <> 0 ORDER BY t.numero_identificacion DESC";
		$DSSaldosAhorros = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSSaldosAhorros)return "";

		return view("reportes.ahorros.saldosModalidad")
					->withEntidad($entidad)
					->withSaldos($DSSaldosAhorros)
					->render();
	}

	/*FIN AHORROS*/

	/*INICIO RECAUDOS*/

	/**
	 * RELACIÓN GENERACIÓN RECAUDOS POR PROCESO
	 * @param type $request
	 * @return type
	 */
	public function generacionRecaudos($request) {
		$validate = Validator::make($request->all(), [
			'numeroProceso'		=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.recaudos.control_procesos,id,deleted_at,NULL'
								]
		]);
		if($validate->fails())return "";
		$controlProceso = ControlProceso::find($request->numeroProceso);
		$entidad = $this->getEntidad();
		if($controlProceso->pagaduria->entidad_id != $entidad->id)abort(401, 'No tiene permiso para ver este recurso');
		$numeroProceso = $request->numeroProceso;
		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, cr.codigo AS concepto, SUM(rn.capital_generado) + SUM(rn.intereses_generado) + SUM(rn.seguro_generado) AS valor FROM recaudos.recaudos_nomina AS rn INNER JOIN general.terceros AS t ON rn.tercero_id=t.id INNER JOIN recaudos.conceptos_recaudos AS cr ON rn.concepto_recaudo_id=cr.id WHERE control_proceso_id = ? GROUP BY t.numero_identificacion, t.nombre, cr.codigo ORDER BY t.numero_identificacion DESC";
		$DSGeneracionRecaudos = DB::select($query, [$numeroProceso]);

		if(!$DSGeneracionRecaudos)return "";

		return view("reportes.recaudos.generacionRecaudos")
					->withEntidad($entidad)
					->withRecaudos($DSGeneracionRecaudos)
					->render();
	}

	/**
	 * DETALLE RECAUDOS DE NÓMINA POR PROCESO
	 * @param type $request
	 * @return type
	 */
	public function detalleRecaudos($request) {
		$validate = Validator::make($request->all(), [
			'numeroProceso'		=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.recaudos.control_procesos,id,deleted_at,NULL'
								]
		]);
		if($validate->fails())return "";
		$controlProceso = ControlProceso::find($request->numeroProceso);
		$entidad = $this->getEntidad();
		if($controlProceso->pagaduria->entidad_id != $entidad->id)abort(401, 'No tiene permiso para ver este recurso');
		$numeroProceso = $request->numeroProceso;
		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, rn.tipo_recaudo AS tipo, CASE WHEN rn.tipo_recaudo = 'AHORRO' THEN ma.nombre WHEN rn.tipo_recaudo = 'CRÉDITO' THEN sc.numero_obligacion END AS concepto, rn.capital_generado AS capitalgenerado, rn.intereses_generado AS interesgenerado, rn.seguro_generado AS segurogenerado, rn.capital_generado + rn.intereses_generado + rn.seguro_generado AS totalgenerado, rn.capital_aplicado + rn.capital_ajustado AS capitalaplicado, rn.intereses_aplicado + rn.intereses_ajustado AS interesesaplicado, rn.seguro_aplicado + rn.seguro_ajustado AS seguroaplicado, rn.capital_aplicado + rn.capital_ajustado + rn.intereses_aplicado + rn.intereses_ajustado + rn.seguro_aplicado + rn.seguro_ajustado AS totalaplicado FROM recaudos.recaudos_nomina AS rn INNER JOIN general.terceros AS t ON rn.tercero_id=t.id LEFT JOIN ahorros.modalidades_ahorros AS ma ON rn.modalidad_id=ma.id LEFT JOIN creditos.solicitudes_creditos AS sc ON rn.solicitud_credito_id=sc.id WHERE rn.control_proceso_id = ? ORDER BY t.numero_identificacion, rn.tipo_recaudo";
		$DSDetalleRecaudos = DB::select($query, [$numeroProceso]);

		if(!$DSDetalleRecaudos)return "";

		return view("reportes.recaudos.detalleRecaudos")
					->withEntidad($entidad)
					->withRecaudos($DSDetalleRecaudos)
					->render();
	}

	/*FIN RECAUDOS*/

	/*INICIO SOCIOS*/
	/**
	 * Resumen general del estado de cuenta de un socio
	 * @param type $request
	 * @return type
	 */
	public function sociosEstadoCuenta($request) {
		$entidad = $this->getEntidad();
		$validate = Validator::make($request->all(), [
			'numeroIdentificacion'	=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.general.terceros,numero_identificacion,entidad_id,' . $entidad->id . ',deleted_at,NULL',
									],
			'fechaConsulta'			=> 'bail|required|date_format:"Y/m/d"',
		]);
		if($validate->fails())return "";
		$tercero = Tercero::entidadTercero($entidad->id)->whereNumeroIdentificacion($request->numeroIdentificacion)->first();
		$socio = optional($tercero)->socio;
		$cupo = $tercero->cupoDisponible(implode('/', array_reverse(explode('/', $request->fechaConsulta))));
		$fechaConsulta = Carbon::createFromFormat('Y/m/d', $request->fechaConsulta);
		$ahorros = collect();
		$creditos = collect();
		if($socio) {
			$res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$socio->id, $fechaConsulta]);
			$ahorros = collect($res);
			$creditos = $tercero->solicitudesCreditos()->where('fecha_desembolso', '<=', $fechaConsulta)->estado('DESEMBOLSADO')->get();
			$contacto = $tercero->contactos->where('es_preferido', 1)->first();
		}
		return view("reportes.socios.sociosEstadoCuenta")
					->withEntidad($entidad)
					->withTer($tercero)
					->withSocio($socio)
					->withCupo($cupo)
					->withAhorros($ahorros)
					->withCreditos($creditos)
					->withFechaConsulta($fechaConsulta)
					->withContacto($contacto)
					->render();
	}

	/**
	 * Socios activos a fecha
	 * @param type $request
	 * @return type
	 */
	public function sociosActivos($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, s.fecha_afiliacion AS afiliacion, p.nombre AS empresa, s.estado AS estado FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id=t.id INNER JOIN recaudos.pagadurias AS p ON s.pagaduria_id=p.id WHERE t.entidad_id = ? AND s.fecha_afiliacion IS NOT NULL AND general.fn_fecha_sin_hora(fecha_afiliacion) <= ? AND (s.fecha_retiro IS NULL OR general.fn_fecha_sin_hora(s.fecha_retiro) > ?) ORDER BY s.estado ASC, t.nombre ASC";
		$DSSociosActivos = DB::select($query, [$entidad->id, $fechaCorte, $fechaCorte ]);
		if(!$DSSociosActivos)return "";

		foreach($DSSociosActivos as &$socios) {
			$socios->afiliacion = Carbon::createFromFormat('Y-m-d H:i:s.000', $socios->afiliacion);
		}

		return view("reportes.socios.sociosActivos")
					->withEntidad($entidad)
					->withSocios($DSSociosActivos)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * AFILIACIONES EN UN RANGO DE TIEMPO
	 * @param type $request
	 * @return type
	 */
	public function sociosAfiliaciones($request) {
		$validate = Validator::make($request->all(), [
			'fechaInicio'		=> 'bail|required|date_format:"Y/m/d"',
			'fechaFinal'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFinal = Carbon::createFromFormat('Y/m/d', $request->fechaFinal)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, s.fecha_afiliacion AS afiliacion, p.nombre AS empresa FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id=t.id INNER JOIN recaudos.pagadurias AS p ON s.pagaduria_id=p.id WHERE t.entidad_id = ? AND general.fn_fecha_sin_hora(s.fecha_afiliacion) BETWEEN ? AND ? ORDER BY t.numero_identificacion DESC";
		$DSSociosAfiliaciones = DB::select($query, [$entidad->id, $fechaInicio, $fechaFinal]);
		if(!$DSSociosAfiliaciones)return "";

		foreach($DSSociosAfiliaciones as &$sociosAfiliaciones) {
			$sociosAfiliaciones->afiliacion = Carbon::createFromFormat('Y-m-d H:i:s.000', $sociosAfiliaciones->afiliacion);
		}

		return view("reportes.socios.sociosAfiliaciones")
					->withEntidad($entidad)
					->withSociosAfiliaciones($DSSociosAfiliaciones)
					->withFechaInicio($fechaInicio)
					->withFechaFinal($fechaFinal)
					->render();
	}

	/**
	 * RETIROS EN UN RANGO DE TIEMPO
	 * @param type $request
	 * @return type
	 */
	public function sociosRetiros($request) {
		$validate = Validator::make($request->all(), [
			'fechaInicio'		=> 'bail|required|date_format:"Y/m/d"',
			'fechaFin'			=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFin)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, sr.fecha_solicitud_retiro AS fecha, cr.tipo_causa_retiro AS tipo, cr.nombre AS causa FROM socios.socios_retiros AS sr INNER JOIN socios.socios AS s ON sr.socio_id=s.id INNER JOIN general.terceros AS t ON s.tercero_id=t.id INNER JOIN socios.causas_retiro AS cr ON sr.causa_retiro_id=cr.id WHERE t.entidad_id = ? AND general.fn_fecha_sin_hora(sr.fecha_solicitud_retiro) BETWEEN ? AND ? ORDER BY sr.fecha_solicitud_retiro";
		$DSSociosRetiros = DB::select($query, [$entidad->id, $fechaInicio, $fechaFin]);
		if(!$DSSociosRetiros)return "";

		foreach($DSSociosRetiros as &$sociosRetiros) {
			$sociosRetiros->fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $sociosRetiros->fecha);
		}

		return view("reportes.socios.sociosRetiros")
					->withEntidad($entidad)
					->withSociosRetiros($DSSociosRetiros)
					->withFechaInicio($fechaInicio)
					->withFechaFin($fechaFin)
					->render();
	}

	/**
	 * INFORMACIÓN GENERAL DE ASOCIADOS
	 * @param type $request
	 * @return type
	 */
	public function generalAsociados($request) {

		$entidad = $this->getEntidad();

		$query = "DECLARE @EntidadId int = ?; WITH socios AS( SELECT s.tercero_id, t.numero_identificacion, t.nombre, sx.nombre AS sexo, t.fecha_nacimiento, p.nombre AS empresa, s.fecha_afiliacion, s.fecha_antiguedad, ec.nombre AS estado_civil, s.fecha_ingreso AS fecha_empresa, s.tipo_contrato, s.sueldo_mes, s.estado FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id=t.id LEFT JOIN general.sexos AS sx ON t.sexo_id=sx.id LEFT JOIN recaudos.pagadurias AS p ON s.pagaduria_id=p.id LEFT JOIN socios.estados_civiles AS ec ON s.estado_civil_id=ec.id WHERE t.entidad_id = @entidadId AND t.esta_activo = 1 AND t.deleted_at IS NULL), contactos AS(SELECT * FROM (SELECT s.tercero_id AS tercero_id, c.direccion, c.movil, c.telefono, c.email, p.nombre AS pais, d.nombre AS departamento, ci.nombre AS ciudad, ROW_NUMBER() OVER(PARTITION BY s.tercero_id ORDER BY s.tercero_id ASC, p.nombre ASC, c.es_preferido ASC) AS preferido FROM general.contactos AS c LEFT JOIN general.ciudades AS ci ON c.ciudad_id = ci.id LEFT JOIN general.departamentos AS d ON ci.departamento_id = d.id LEFT JOIN general.paises AS p ON d.pais_id = p.id INNER JOIN socios AS s on c.tercero_id = s.tercero_id WHERE c.deleted_at IS NULL) AS c WHERE c.preferido = 1) SELECT s.numero_identificacion AS identificacion, s.nombre AS nombre, s.estado AS estado, s.sexo AS sexo, s.fecha_nacimiento AS nacimiento, s.empresa AS empresa, s.fecha_afiliacion AS afiliacion, s.fecha_antiguedad AS antiguedad, s.estado_civil AS ecivil, s.fecha_empresa AS iempresa, s.tipo_contrato AS contrato, s.sueldo_mes AS sueldo, c.pais AS pais, c.departamento AS departamento, c.ciudad AS ciudad, c.direccion AS direccion, c.telefono as telefono, c.movil as movil, c.email as email FROM socios AS s LEFT JOIN contactos AS c ON s.tercero_id=c.tercero_id";
		$DSGeneralAsociados = DB::select($query, [$entidad->id]);
		if(!$DSGeneralAsociados)return "";

		foreach($DSGeneralAsociados as &$generalAsociados) {
			try {
				if(!is_null($generalAsociados->nacimiento))
					$generalAsociados->nacimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $generalAsociados->nacimiento);

				if(!is_null($generalAsociados->afiliacion))
					$generalAsociados->afiliacion = Carbon::createFromFormat('Y-m-d H:i:s.000', $generalAsociados->afiliacion);

				if(!is_null($generalAsociados->antiguedad))
					$generalAsociados->antiguedad = Carbon::createFromFormat('Y-m-d H:i:s.000', $generalAsociados->antiguedad);

				if(!is_null($generalAsociados->iempresa))
					$generalAsociados->iempresa = Carbon::createFromFormat('Y-m-d H:i:s.000', $generalAsociados->iempresa);
			}
			catch(\InvalidArgumentException $e) {
			}
		}

		return view("reportes.socios.generalAsociados")
					->withEntidad($entidad)
					->withGeneralAsociados($DSGeneralAsociados)
					->render();
	}


	/*FIN SOCIOS*/

	/*INICIO CRÉDITOS*/

	/**
	 * Estudio de créditos
	 * @param type $request
	 * @return type
	 */
	public function creditosEstudioDeCredito($request) {
		$entidad = $this->getEntidad();
		$validate = Validator::make($request->all(), [
			'numeroRadicado'	=> [
				'bail',
				'required',
				'integer',
				'min:1',
				'max:2147483647',
				'exists:sqlsrv.creditos.solicitudes_creditos,id,entidad_id,' .
				$entidad->id . ',deleted_at,NULL',
			]
		]);
		try {
			if($validate->fails()) {
				return "";
			}
		}
		catch(QueryException $e) {
			return "";
		}
		$solicitud = SolicitudCredito::find($request->numeroRadicado);
		$tercero = $solicitud->tercero;
		$socio = $tercero->socio;
		$creditosRecogidos = $solicitud->obligacionesConsolidadas()->with('creditoConsolidado')->get();
		$codeudores = $solicitud->getCodeudores($solicitud);
		$modalidades = ModalidadAhorro::entidadId($entidad->id)->whereEsReintegrable(true)->where('apalancamiento_cupo', '>', 0)->get();
		$creditos = $tercero->solicitudesCreditos()->whereEstadoSolicitud('DESEMBOLSADO')->whereFormaPago('NOMINA')->get();
		$endeudamiento = collect();
		$endeudamiento->push(['concepto' => 'Sueldo', 'ingresos' => $socio->sueldo_mes, 'deducciones' => 0]);
		$endeudamiento->push(['concepto' => 'Comisiones', 'ingresos' => ConversionHelper::conversionValorPeriodicidad($socio->valor_comision, $socio->periodicidad_comision, 'MENSUAL'), 'deducciones' => 0]);
		$incluyeCuotas = ParametroInstitucional::entidadId($entidad->id)->codigo('CR004')->first();
		if(optional($incluyeCuotas)->indicador) {
			foreach($socio->cuotasObligatorias as $cuota) {
				$endeudamiento->push([
					'concepto' => $cuota->modalidadAhorro->nombre,
					'ingresos' => 0,
					'deducciones' => $cuota->tipo_calculo == 'PORCENTAJESUELDO' ? (($socio->sueldo_mes * $cuota->valor) / 100) : $cuota->valor
				]);
			}
		}
		$incluyeCuotas = ParametroInstitucional::entidadId($entidad->id)->codigo('CR005')->first();
		if(optional($incluyeCuotas)->indicador) {
			foreach($socio->cuotasVoluntarias as $cuotaVoluntaria) {
				$endeudamiento->push([
					'concepto' => $cuotaVoluntaria->modalidadAhorro->nombre,
					'ingresos' => 0,
					'deducciones' => ConversionHelper::conversionValorPeriodicidad($cuotaVoluntaria->valor, $cuotaVoluntaria->periodicidad, 'MENSUAL')
				]);
			}
		}
		foreach($creditos as $credito) {
			$saldo = $credito->saldoObligacion($solicitud->fecha_solicitud);
			if($saldo != 0) {
				$endeudamiento->push([
					'concepto' => str_limit($credito->numero_obligacion . ' - ' . $credito->modalidadCredito->nombre, 40),
					'ingresos' => 0,
					'deducciones' => ConversionHelper::conversionValorPeriodicidad($credito->valor_cuota, $credito->periodicidad, 'MENSUAL')
				]);
			}
		}
		if($socio->descuentos_nomina) {
			$endeudamiento->push([
				'concepto' => 'Deducciones externas',
				'ingresos' => 0,
				'deducciones' => ConversionHelper::conversionValorPeriodicidad($socio->descuentos_nomina, $socio->periodicidad_descuentos_nomina, 'MENSUAL')
			]);
		}
		if($solicitud->forma_pago == 'PRIMA') {
			$endeudamiento->push([
				'concepto' => 'Cuota nuevo crédito',
				'ingresos' => 0,
				'deducciones' => 0
			]);
		}
		else {
			$endeudamiento->push([
				'concepto' => 'Cuota nuevo crédito',
				'ingresos' => 0,
				'deducciones' => ConversionHelper::conversionValorPeriodicidad($solicitud->valor_cuota, $solicitud->periodicidad, 'MENSUAL')
			]);
		}
		$porcentajeMaximoEndeudamiento = ParametroInstitucional::entidadId($entidad->id)->codigo('CR003')->first();
		$porcentajeMaximoEndeudamiento = $porcentajeMaximoEndeudamiento ? $porcentajeMaximoEndeudamiento->valor : 100;

		//SDATs
		$sql = "SELECT ts.nombre, SUM(ms.valor) AS valor, ts.apalancamiento_cupo FROM ahorros.movimientos_sdat AS ms INNER JOIN ahorros.sdats AS s ON ms.sdat_id = s.id INNER JOIN ahorros.tipos_sdat AS ts ON s.tipo_sdat_id = ts.id WHERE s.socio_id = ? AND	general.fn_fecha_sin_hora(ms.fecha_movimiento) <= ? GROUP BY ts.nombre, ts.apalancamiento_cupo;";
		$sdats = DB::select($sql, [$socio->id, Carbon::createFromFormat('d/m/Y', '31/12/2100')]);
		$dataSDAT = collect();
		foreach($sdats as $sdat) {
			$sdatObj = (object)[
				"nombre"				=> $sdat->nombre,
				"saldo"					=> floatval($sdat->valor),
				"apalancamiento_cupo"	=> floatval($sdat->apalancamiento_cupo)
			];
			$dataSDAT->push($sdatObj);
		}

		return view("reportes.creditos.creditosEstudioDeCredito")
			->withEntidad($entidad)
			->withSolicitud($solicitud)
			->withTer($tercero)
			->withSocio($socio)
			->withCreditosRecogidos($creditosRecogidos)
			->withCodeudores($codeudores)
			->withModalidades($modalidades)
			->withCreditos($creditos)
			->withEndeudamientos($endeudamiento)
			->withPorcentajeMaximoEndeudamiento($porcentajeMaximoEndeudamiento)
			->withSdats($dataSDAT)
			->render();
	}

	/**
	 * SALDOS DE CARTERA POR OBLIGACION
	 * @param type $request
	 * @return type
	 */
	public function saldosCarteraObligacion($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, sc.numero_obligacion AS obligacion, m.nombre AS modalidad, sc.fecha_desembolso AS fecha, sc.valor_credito AS valorinicial, sc.tasa AS tasa, sc.plazo AS plazo, sc.valor_cuota AS valorcuota, sc.fecha_primer_pago AS fechainicio,	SUM(mc.valor_movimiento) AS saldo FROM creditos.solicitudes_creditos AS sc INNER JOIN general.terceros AS t ON sc.tercero_id=t.id INNER JOIN creditos.movimientos_capital_credito AS mc ON sc.id=mc.solicitud_credito_id INNER JOIN creditos.modalidades AS m ON sc.modalidad_credito_id=m.id WHERE	sc.entidad_id = ? AND general.fn_fecha_sin_hora(mc.fecha_movimiento) <= ? AND sc.estado_solicitud in ('DESEMBOLSADO', 'SALDADO') GROUP BY sc.id, t.numero_identificacion,	t.nombre, sc.numero_obligacion, sc.valor_credito, sc.fecha_desembolso, m.nombre, sc.tasa, sc.plazo, sc.valor_cuota, sc.fecha_primer_pago HAVING	SUM(mc.valor_movimiento) <> 0";
		$DSSaldosObligacion = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSSaldosObligacion)return "";

		foreach($DSSaldosObligacion as &$saldosobligacion) {
			try {
				if(!is_null($saldosobligacion->fecha))
				$saldosobligacion->fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $saldosobligacion->fecha);

				if(!is_null($saldosobligacion->fechainicio))
				$saldosobligacion->fechainicio = Carbon::createFromFormat('Y-m-d H:i:s.000', $saldosobligacion->fechainicio);
			}
			catch(\InvalidArgumentException $e) {
				//dd($saldosConsolidados);
			}
		}

		return view("reportes.creditos.saldosCarteraObligacion")
					->withEntidad($entidad)
					->withSaldosObligacion($DSSaldosObligacion)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * COLOCACIONES DE CRÉDITOS
	 * @param type $request
	 * @return type
	 */
	public function colocacionesCreditos($request) {
		$validate = Validator::make($request->all(), [
			'fechaInicio'		=> 'bail|required|date_format:"Y/m/d"',
			'fechaFin'			=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFin)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, sc.numero_obligacion AS obligacion, m.nombre AS modalidad, sc.valor_credito AS valor,	COALESCE((SUM(oc.pago_capital) + SUM(oc.pago_intereses)), 0) AS consolidado, sc.valor_credito - COALESCE((SUM(oc.pago_capital) + SUM(oc.pago_intereses)), 0) AS neto, sc.fecha_desembolso AS fecha, sc.tasa AS tasa, sc.plazo AS plazo FROM creditos.solicitudes_creditos AS sc	INNER JOIN creditos.modalidades AS m ON sc.modalidad_credito_id=m.id INNER JOIN general.terceros AS t ON sc.tercero_id=t.id	LEFT JOIN creditos.obligaciones_consolidacion AS oc ON oc.solicitud_credito_id=sc.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(fecha_desembolso) BETWEEN ? AND ? AND estado_solicitud IN ('DESEMBOLSADO', 'SALDADO') GROUP BY t.numero_identificacion, t.nombre, sc.numero_obligacion, m.nombre, sc.valor_credito, sc.fecha_desembolso, sc.tasa, sc.plazo ORDER BY sc.fecha_desembolso";
		$DSColocacionesCreditos = DB::select($query, [$entidad->id, $fechaInicio, $fechaFin ]);
		if(!$DSColocacionesCreditos)return "";

		foreach($DSColocacionesCreditos as &$colocaciones) {
			$colocaciones->fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $colocaciones->fecha);
		}

		return view("reportes.creditos.colocacionesCreditos")
					->withEntidad($entidad)
					->withColocacionesCreditos($DSColocacionesCreditos)
					->withFechaInicio($fechaInicio)
					->withFechaFin($fechaFin)
					->render();
	}

	/**
	 * SALDOS CONSOLIDADOS DE CARTERA
	 * @param type $request
	 * @return type
	 */
	public function saldosConsolidados($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, t.fecha_nacimiento AS nacimiento, SUM(mk.valor_movimiento) AS saldo FROM creditos.movimientos_capital_credito AS mk INNER JOIN creditos.solicitudes_creditos AS sc ON mk.solicitud_credito_id=sc.id INNER JOIN general.terceros AS t ON sc.tercero_id=t.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(mk.fecha_movimiento) <= ? GROUP BY t.numero_identificacion, t.nombre, t.fecha_nacimiento HAVING SUM(mk.valor_movimiento) <> 0";
		$DSSaldosConsolidados = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSSaldosConsolidados)return "";

		foreach($DSSaldosConsolidados as &$saldosConsolidados) {
			try {
				if(!is_null($saldosConsolidados->nacimiento))
				$saldosConsolidados->nacimiento = Carbon::createFromFormat('Y-m-d H:i:s.000', $saldosConsolidados->nacimiento);
			}
			catch(\InvalidArgumentException $e) {
				//dd($saldosConsolidados);
			}
		}

		return view("reportes.creditos.saldosConsolidados")
					->withEntidad($entidad)
					->withSaldosConsolidados($DSSaldosConsolidados)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * SALDOS DE CARTERA RETIRADOS
	 * @param type $request
	 * @return type
	 */
	public function saldosCarteraRetirados($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT t.numero_identificacion AS identificacion, t.nombre AS nombre, s.estado AS estado, sc.numero_obligacion AS obligacion, m.nombre AS modalidad, sc.fecha_desembolso AS fecha, sc.valor_credito AS valorinicial, sc.tasa AS tasa, sc.plazo AS plazo, sc.valor_cuota AS valorcuota, SUM(mc.valor_movimiento) AS saldo FROM creditos.solicitudes_creditos AS sc INNER JOIN general.terceros AS t ON sc.tercero_id=t.id INNER JOIN creditos.movimientos_capital_credito AS mc ON sc.id=mc.solicitud_credito_id INNER JOIN creditos.modalidades AS m ON sc.modalidad_credito_id=m.id LEFT JOIN socios.socios AS s ON s.tercero_id=t.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(mc.fecha_movimiento) <= ? AND sc.estado_solicitud = 'DESEMBOLSADO' AND (s.estado <> 'ACTIVO' OR s.estado IS NULL) GROUP BY sc.id, t.numero_identificacion, t.nombre, sc.numero_obligacion, sc.valor_credito, sc.fecha_desembolso, m.nombre, sc.tasa, sc.plazo, sc.valor_cuota, s.estado HAVING SUM(mc.valor_movimiento) <> 0";
		$DSSaldosRetirados = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSSaldosRetirados)return "";

		foreach($DSSaldosRetirados as &$saldosRetirados) {
			$saldosRetirados->fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $saldosRetirados->fecha);
		}
		return view("reportes.creditos.saldosCarteraRetirados")
					->withEntidad($entidad)
					->withSaldosRetirados($DSSaldosRetirados)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * CODEUDORES
	 * @param type $request
	 * @return type
	 */
	public function codeudores($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT tc.numero_identificacion AS idcodeudor, tc.nombre AS codeudor, sc.numero_obligacion AS obligacion, sc.valor_credito AS valorinicial, sc.fecha_desembolso AS fechadesembolso, sc.tasa AS tasa, sc.calificacion_obligacion AS calificacion, SUM(mcc.valor_movimiento) AS saldo, td.numero_identificacion AS iddeudor, td.nombre AS deudor FROM creditos.codeudores AS cod INNER JOIN general.terceros AS tc ON cod.tercero_id=tc.id INNER JOIN creditos.solicitudes_creditos AS sc ON cod.solicitud_credito_id=sc.id INNER JOIN creditos.movimientos_capital_credito AS mcc ON mcc.solicitud_credito_id=sc.id INNER JOIN general.terceros AS td ON sc.tercero_id=td.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(mcc.fecha_movimiento) <= ? GROUP BY tc.numero_identificacion, tc.nombre, sc.numero_obligacion, sc.valor_credito, sc.fecha_desembolso, sc.tasa, sc.calificacion_obligacion, td.numero_identificacion, td.nombre HAVING SUM(mcc.valor_movimiento) <> 0 ORDER BY tc.numero_identificacion";
		$DSCodeudores = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSCodeudores)return "";

		foreach($DSCodeudores as &$codeudores) {
			$codeudores->fechadesembolso = Carbon::createFromFormat('Y-m-d H:i:s.000', $codeudores->fechadesembolso);
		}

		return view("reportes.creditos.codeudores")
					->withEntidad($entidad)
					->withCodeudores($DSCodeudores)
					->withFechaCorte($fechaCorte)
					->render();
	}



	/**
	 * REPORTE TRANSUNION
	 * @param type $request
	 * @return type
	 */
	public function transunion($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "DECLARE @entidad AS INT = ?; DECLARE @fecha AS DATETIME = ?; DECLARE @modulo AS INT = 7; WITH contactos AS(SELECT c.tercero_id, ci.codigo AS codigo_ciudad, ci.nombre AS nombre_ciudad, d.codigo AS codigo_departamento, d.nombre AS nombre_departamento, COALESCE(c.movil, c.telefono, '') AS telefono, COALESCE(LEFT(c.direccion, 49), '') AS direccion, COALESCE(c.email, '') AS email, ROW_NUMBER() OVER(PARTITION BY c.tercero_id ORDER BY c.es_preferido DESC) AS RNK FROM general.contactos AS c INNER JOIN general.ciudades AS ci ON c.ciudad_id = ci.id INNER JOIN general.departamentos AS d ON ci.departamento_id = d.id INNER JOIN general.terceros AS t ON c.tercero_id = t.id WHERE c.deleted_at IS NULL AND t.entidad_id = @entidad) SELECT CASE ti.codigo WHEN 'NIT' THEN 2 WHEN 'CE' THEN 3 WHEN 'TI' THEN 4 ELSE 1 END AS tipo_identificacion, t.numero_identificacion AS identificacion, t.nombre AS nombre, '' AS reservado, CONVERT(NVARCHAR, @fecha, 112) AS limite_pago, cc.numero_obligacion AS obligacion, 1 AS sucursal, 'P' AS calidad, CASE WHEN cc.calificacion_final = 'A' THEN '1' WHEN cc.calificacion_final = 'B' THEN '2' WHEN cc.calificacion_final = 'C' THEN '3' WHEN cc.calificacion_final = 'D' THEN '4' WHEN cc.calificacion_final = 'E' THEN '5' WHEN cc.calificacion_final = 'K' THEN '6' END AS calificacion, '6' AS titular, CASE WHEN CC.estado_solicitud = 'SALDADO' THEN '7' ELSE '1' END AS estadoobl, CASE WHEN cc.dias_vencidos BETWEEN 0 AND 29 THEN '0' WHEN cc.dias_vencidos BETWEEN 30 AND 59 THEN '1' WHEN cc.dias_vencidos BETWEEN 60 AND 89 THEN '2' WHEN cc.dias_vencidos BETWEEN 90 AND 119 THEN '3' WHEN cc.dias_vencidos BETWEEN 120 AND 149 THEN '4' WHEN cc.dias_vencidos BETWEEN 150 AND 179 THEN '5' WHEN cc.dias_vencidos BETWEEN 180 AND 209 THEN '6'  WHEN cc.dias_vencidos BETWEEN 210 AND 239 THEN '7' WHEN cc.dias_vencidos BETWEEN 240 AND 269 THEN '8' WHEN cc.dias_vencidos BETWEEN 270 AND 299 THEN '9' WHEN cc.dias_vencidos >= 300 THEN '10' END AS mora, '' AS anos_mora, CONVERT(NVARCHAR, @fecha, 112) AS corte, CONVERT(NVARCHAR, cc.fecha_desembolso, 112) AS fecha_inicio, CONVERT(NVARCHAR, cc.fecha_terminacion_programada, 112) AS fecha_terminacion, CONVERT(NVARCHAR, cc.fecha_desembolso, 112) AS fecha_exigibilidad, '' AS prescripcion, CASE WHEN CC.estado_solicitud = 'SALDADO' THEN CONVERT(NVARCHAR, cc.fecha_cancelacion, 112) ELSE '' END AS fecha_pago, '' AS extincion, CASE WHEN CC.estado_solicitud = 'SALDADO' THEN '1' ELSE '' END AS tipo_pago, CASE WHEN cc.periodicidad = 'SEMANAL' THEN '1' WHEN cc.periodicidad = 'CATORCENAL' THEN '4' WHEN cc.periodicidad = 'QUINCENAL' THEN '4' ELSE '7' END AS periodicidad, '0' AS probabilidad, cc.altura_cuota AS numero_cuotas_pagas, cc.plazo AS cuotas_pactadas, IIF(cc.capital_vencido < 0, 0, CONVERT(INT, cc.capital_vencido / case when cc.valor_cuota <> 0 then cc.valor_cuota else 1 end)) AS cuotas_vencidas, CONVERT(INT, cc.valor_credito / 1000) AS valor_inicial, IIF(cc.capital_vencido < 0, 0, CONVERT(INT, cc.capital_vencido / 1000)) AS valor_mora, CASE WHEN CC.estado_solicitud = 'DESEMBOLSADO' AND CONVERT(INT, cc.saldo_capital / 1000) <= 0 THEN 1 ELSE CONVERT(INT, cc.saldo_capital / 1000) END saldo, CONVERT(INT, cc.valor_cuota / 1000) AS valor_cuota, '' AS cargo_fijo, '4' AS linea_credito, '' AS permanencia, '1' AS tipo_contrato, '1' AS estado_contrato, '' AS vigencia_contrato, '' AS meses_contrato, '0' AS naturaleza_juridica, '2' AS modalidad_credito, '1' AS tipo_moneda, '2' AS tipo_garantia, '' AS valor_garantia, '2' AS reestructurada, '' AS naturaleza_reestructuracion, '' AS numero_reestructuraciones, '' AS clase_tarjeta, '' AS cheques_devueltos, '' AS categoria_servicios, '' AS plazo, '' AS dias_cartera, '' AS tipo_cuenta, '' AS cupo_sobregiro, '' AS dias_autorizados, con.direccion AS direccion_casa, con.telefono AS telefono_casa, con.codigo_ciudad AS codigo_ciudad_casa, con.nombre_ciudad AS ciudad_casa, con.codigo_departamento AS codigo_departamento, con.nombre_departamento AS departamento FROM general.terceros AS t INNER JOIN general.tipos_identificacion AS ti ON t.tipo_identificacion_id = ti.id INNER JOIN creditos.cierres_cartera AS cc ON cc.tercero_id=t.id INNER JOIN general.control_cierre_modulos AS cm ON cc.control_cierre_modulo_id=cm.id LEFT JOIN contactos AS con ON con.tercero_id = t.id WHERE t.entidad_id = @entidad AND cm.entidad_id = @entidad AND cm.modulo_id = @modulo AND general.fn_fecha_sin_hora(cm.fecha_cierre) = @fecha AND (con.RNK IS NULL OR con.RNK = 1)";
		$DSTransuniones = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSTransuniones)return "";

		/*foreach($DSTransuniones as &$transunion) {
			$transunion->fechadesembolso = Carbon::createFromFormat('Y-m-d H:i:s.000', $transunion->fechadesembolso);
		}*/

		return view("reportes.creditos.transunion")
					->withEntidad($entidad)
					->withTransuniones($DSTransuniones)
					->withFechaCorte($fechaCorte)
					->render();
	}

	/**
	 * INFORME CIERRE DE CARTERA POR PERIODO
	 * @param type $request
	 * @return type
	 */
	public function reporteCierreCartera($request) {
		$validate = Validator::make($request->all(), [
			'fechaCorte'		=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaCorte = Carbon::createFromFormat('Y/m/d', $request->fechaCorte)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "DECLARE @entidad INT = ?; DECLARE @fecha_corte DATETIME = ?; SELECT cc.tercero_numero_identificacion AS Identificacion, cc.tercero_nombre AS Nombre, cc.socio_estado AS Estado, p.nombre AS Empresa, cc.numero_obligacion AS Obligacion, cc.fecha_desembolso AS Desembolso, cc.valor_credito AS Monto, cc.tasa AS Tasa, cc.plazo AS Plazo, cc.valor_cuota AS Cuota, cc.altura_cuota AS Altura, cc.numero_cuotas_pendientes AS Pendientes, cc.modalidad_codigo AS ModalidadCodigo, cc.modalidad_nombre AS Modalidad, cc.saldo_capital AS Saldo, cc.saldo_intereses AS SaldoInteres, cc.interes_causado AS InteresCausado, cc.saldo_seguro AS SaldoSeguro, cc.dias_vencidos AS DiasVencidos, cc.capital_vencido AS CapitalVencido, cc.tipo_cartera AS Cartera, cc.tipo_garantia AS TipoGarantia, cc.forma_pago AS TipoPago, cc.periodicidad AS Frecuencia, cc.fecha_descuento_capital AS InicioPago, cc.fecha_terminacion_programada AS FechaFinal, cc.fecha_ultimo_pago AS UltimoPago, cc.calificacion_periodo_anterior AS CalificacionAnterior, cc.calificacion_actual AS CalificacionActual, cc.calificacion_final AS CalificacionFinal, cc.valor_aporte_deterioro AS AhorrosDeterioro, cc.base_deterioro AS BaseDeterioro, cc.deterioro_capital AS DeterioroCapital, cc.deterioro_intereses AS DeterioroIntereses, cc.fecha_cancelacion AS FechaCancelación, cc.estado_solicitud AS EstadoCredito, cc.cuif_capital AS CuentaCapital FROM creditos.cierres_cartera AS cc INNER JOIN general.control_cierre_modulos AS ccm ON cc.control_cierre_modulo_id = ccm.id LEFT JOIN recaudos.pagadurias AS p ON cc.pagaduria_id = p.id WHERE cc.entidad_id = @entidad AND ccm.fecha_cierre = @fecha_corte";
		$DSCierreCartera = DB::select($query, [$entidad->id, $fechaCorte ]);
		if(!$DSCierreCartera)return "";

		foreach($DSCierreCartera as &$cierrecartera) {
			try {
				if(!is_null($cierrecartera->Desembolso))
				$cierrecartera->Desembolso = Carbon::createFromFormat('Y-m-d H:i:s.000', $cierrecartera->Desembolso);

				if(!is_null($cierrecartera->InicioPago))
				$cierrecartera->InicioPago = Carbon::createFromFormat('Y-m-d H:i:s.000', $cierrecartera->InicioPago);
			
				if(!is_null($cierrecartera->FechaFinal))
				$cierrecartera->FechaFinal = Carbon::createFromFormat('Y-m-d H:i:s.000', $cierrecartera->FechaFinal);
			
				if(!is_null($cierrecartera->UltimoPago))
				$cierrecartera->UltimoPago = Carbon::createFromFormat('Y-m-d H:i:s.000', $cierrecartera->UltimoPago);
			
				if(!is_null($cierrecartera->FechaCancelación))
				$cierrecartera->FechaCancelación = Carbon::createFromFormat('Y-m-d H:i:s.000', $cierrecartera->FechaCancelación);
			}
			catch(\InvalidArgumentException $e) {
			}
		}

		return view("reportes.creditos.reporteCierreCartera")
					->withEntidad($entidad)
					->withCierreCartera($DSCierreCartera)
					->withFechaCorte($fechaCorte)
					->render();
	}


	/*FIN CRÉDITOS*/

	/*INICIO CONTROL Y VIGILANCIA*/

	/**
	 * BARRIDO LISTAS DE CONTROL
	 * @param type $request
	 * @return type
	 */
	public function controlVigilanciaBarridoListasControl() {
		$entidad = $this->getEntidad();

		$query = "exec controlVigilancia.sp_barrido_listas_control ?";
		$DSBarrido = DB::select($query, [$entidad->id]);
		if(!$DSBarrido)return "";
		foreach ($DSBarrido as &$item) {
			$diff = substr($item->fecha_lista, 0, 10);
			try {
				$item->fecha_lista = Carbon::createFromFormat('Y-m-d', $diff)->startOfDay();
			}
			catch(\InvalidArgumentException $e){
				$item->fecha_lista = "";
			}
			$item->es_tercero = $item->es_tercero ? 'Sí' : 'No';
			$item->es_asociado = $item->es_asociado ? 'Sí' : 'No';
			$item->es_empleado = $item->es_empleado ? 'Sí' : 'No';
			$item->es_proveedor = $item->es_proveedor ? 'Sí' : 'No';
			$item->es_pep = $item->es_pep ? 'Sí' : 'No';
		}

		return view("reportes.controlVigilancia.barridoListasControl")
					->withEntidad($entidad)
					->withBarrido($DSBarrido)
					->withFechaGeneracion(Carbon::now())
					->render();
	}

	/**
	 * CHEQUEO EN LISTA POR RANGO DE TIEMPO
	 * @param type $request
	 * @return type
	 */
	public function chequeosListas($request) {
		$validate = Validator::make($request->all(), [
			'fechaInicio'		=> 'bail|required|date_format:"Y/m/d"',
			'fechaFin'			=> 'bail|required|date_format:"Y/m/d"',
		]);

		if($validate->fails())return "";

		$fechaInicio = Carbon::createFromFormat('Y/m/d', $request->fechaInicio)->startOfDay();
		$fechaFin = Carbon::createFromFormat('Y/m/d', $request->fechaFin)->startOfDay();
		$entidad = $this->getEntidad();

		$query = "SELECT created_at AS fecha_proceso, usuario AS usuario, numero_identificacion AS identificacion, primer_nombre AS primer_nombre, segundo_nombre AS segundo_nombre, primer_apellido AS primer_apellido, segundo_apellido AS segundo_apellido, es_tercero AS tercero, es_asociado AS asociado, es_empleado AS empleado, es_proveedor AS proveedor, es_pep AS pep, departamento AS departamento, ciudad AS ciudad, porcentaje_coincidencia AS coincidencia, tipo_coincidencia AS tipo, tipo_lista AS lista, fecha_lista AS fecha_lista, numero_documento AS documento_lista, lista_primer_nombre AS lista_primer_nombre, lista_segundo_nombre AS lista_segundo_nombre, lista_primer_apellido AS lista_primer_apellido, lista_segundo_apellido AS lista_segundo_apellido FROM controlVigilancia.chequeos_listas_control WHERE entidad_id = ? AND general.fn_fecha_sin_hora(created_at) BETWEEN ? AND ?";
		$DSChequeosListas = DB::select($query, [$entidad->id, $fechaInicio, $fechaFin ]);
		if(!$DSChequeosListas)return "";

		foreach ($DSChequeosListas as &$chequeos) {

			$chequeos->tercero = $chequeos->tercero ? 'Sí' : 'No';
			$chequeos->asociado = $chequeos->asociado ? 'Sí' : 'No';
			$chequeos->empleado = $chequeos->empleado ? 'Sí' : 'No';
			$chequeos->proveedor = $chequeos->proveedor ? 'Sí' : 'No';
			$chequeos->pep = $chequeos->pep ? 'Sí' : 'No';

		try {
				if(!is_null($chequeos->fecha_lista))
				$chequeos->fecha_lista = Carbon::createFromFormat('Y-m-d H:i:s.000', $chequeos->fecha_lista);
			}
			catch(\InvalidArgumentException $e) {
				//dd($saldosConsolidados);
			}

		}

		return view("reportes.controlVigilancia.chequeosListas")
					->withEntidad($entidad)
					->withChequeosListas($DSChequeosListas)
					->withFechaInicio($fechaInicio)
					->withFechaFin($fechaFin)
					->render();
	}

	/*fin CONTROL Y VIGILANCIA*/

	public function getFechaParaMostrar($fecha) {
		$fechaMostrar = "%s %s";
		$mes = "";
		switch ($fecha->month) {
			case 1: $mes = "Enero"; break;
			case 2: $mes = "Febrero"; break;
			case 3: $mes = "Marzo"; break;
			case 4: $mes = "Abril"; break;
			case 5: $mes = "Mayo"; break;
			case 6: $mes = "Junio"; break;
			case 7: $mes = "Julio"; break;
			case 8: $mes = "Agosto"; break;
			case 9: $mes = "Septiembre"; break;
			case 10: $mes = "Octubre"; break;
			case 11: $mes = "Noviembre"; break;
			case 12: $mes = "Diciembre"; break;
		}
		return sprintf($fechaMostrar, $mes, $fecha->year);
	}

	/**
	 * Rutas de reportes
	 * @return type
	 */
	public static function routes()
	{
		Route::get('reportes', 'Reportes\ReportesController@index');
		Route::get('reportes/estadisticos', 'Reportes\ReportesController@reportesEstadisticos');
		Route::get('reportes/{obj}', 'Reportes\ReportesController@getReporte')->name('reportesReporte');
	}
}
