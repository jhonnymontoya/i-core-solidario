<?php

namespace App\CierreCartera;

use App\Models\Ahorros\MovimientoAhorro;
use App\Models\General\Entidad;
use App\Models\Socios\Socio;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class Ahorros
{

	private $entidad = null;
	private $fechaComparacion = null;

	/**
	 * Constructor
	 */
	public function __construct($entidad, $fechaComparacion) {
		$this->entidad = $entidad;
		$this->fechaComparacion = $fechaComparacion;

		if(!($this->entidad instanceof Entidad)) {
			throw new Exception("Se espera una entidad válida", 1);			
		}

		if(!($this->fechaComparacion instanceof Carbon)) {
			throw new Exception("Se espera una fecha válida", 1);			
		}
	}

	/**
	 * Obtiene array con ahorros negativos
	 * Se obtienen los ahorros negativos
	 * @return array
	 */
	public function obtenerAhorrosNegativos() {
		$ahorrosNegativos = ['A' => [], 'B' => [], 'C' => []];
		$movimientoAhorrosNegativos = MovimientoAhorro::entidadId($this->entidad->id)
			->select(DB::raw('socio_id, modalidad_ahorro_id, sum(valor_movimiento) as saldo'))
			->where(DB::raw('DATEADD(dd, DATEDIFF(dd, 0, fecha_movimiento), 0)'), '<=', $this->fechaComparacion)
			->groupBy('socio_id', 'modalidad_ahorro_id')
			->having(DB::raw('sum(valor_movimiento)'), '<', 0)
			->with('socio.tercero')
			->get();
		foreach ($movimientoAhorrosNegativos as $ahorro) {
			array_push($ahorrosNegativos['A'], $ahorro);
		}
		return $ahorrosNegativos;
	}

	/**
	 * Obtiene array con socios liquidados y ahorros
	 * Se obtinen los socios liquidados con ahorros en saldo
	 * @return array
	 */
	public function obtenerSociosLiquidadosConAhorros() {
		$sociosLiquidadosConAhorros = ['A' => [], 'B' => [], 'C' => []];
		$socios = Socio::entidad($this->entidad->id)
			->whereEstado('LIQUIDADO')
			->where('fecha_retiro', '<=', $this->fechaComparacion)
			->whereHas('movimientosAhorros', function($query) {
				$query->where(DB::raw('DATEADD(dd, DATEDIFF(dd, 0, fecha_movimiento), 0)'), '<=', $this->fechaComparacion)
					->groupBy('socio_id', 'modalidad_ahorro_id')
					->having(DB::raw('sum(valor_movimiento)'), '>', 0);
			})
			->with('tercero')
			->get();
		foreach ($socios as $socio) {
			if(!empty($socio->fecha_retiro)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->fecha_retiro, false);
				if($diasDiferencia > 60) {
					array_push($sociosLiquidadosConAhorros['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosLiquidadosConAhorros['B'], $socio);   
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosLiquidadosConAhorros['C'], $socio);
				}
			}
			else {
				array_push($sociosLiquidadosConAhorros['A'], $socio);
			}
		}
		return $sociosLiquidadosConAhorros;
	}

	/**
	 * Obtiene array diferencias entre los ahorros y la contabilidad
	 * Se obtienen diferencias entre movimientos de contabilidad y ahorros
	 * @return array
	 */
	public function obtenerDiferenciaAhorrosContabilidad() {
		$diferenciasAhorrosContabilidad = ['A' => [], 'B' => [], 'C' => []];

		$queryAhorros = "SELECT m.cuif_id, SUM(ma.valor_movimiento) AS saldo FROM ahorros.movimientos_ahorros AS ma INNER JOIN ahorros.modalidades_ahorros AS m ON ma.modalidad_ahorro_id = m.id WHERE m.entidad_id = ? AND DATEADD(dd, DATEDIFF(dd, 0, ma.fecha_movimiento), 0) <= ? GROUP BY m.cuif_id";
		$queryAhorros = DB::select($queryAhorros, [$this->entidad->id, $this->fechaComparacion]);
		$idsCuif = array();
		foreach ($queryAhorros as $contabilidad)$idsCuif[] = $contabilidad->cuif_id;

		$queryContabilidad = sprintf("SELECT dm.cuif_id, SUM(dm.credito) - SUM(dm.debito) AS saldo FROM contabilidad.detalle_movimientos AS dm WHERE dm.entidad_id = ? AND dm.cuif_id IN (%s) AND DATEADD(dd, DATEDIFF(dd, 0, dm.fecha_movimiento), 0) <= ? GROUP BY dm.cuif_id", implode(',', $idsCuif));
		$queryContabilidad = DB::select($queryContabilidad, [$this->entidad->id, $this->fechaComparacion]);
		foreach ($queryAhorros as $ahorro) {
			foreach ($queryContabilidad as $key => $value) {
				if($ahorro->cuif_id == $value->cuif_id) {
					if($ahorro->saldo != $value->saldo) {
						array_push($diferenciasAhorrosContabilidad['A'], 'Diferencia hallada entre módulo contable y ahorro');
						break;
					}
					else {
						unset($queryContabilidad[$key]);
					}
				}
			}
		}
		return $diferenciasAhorrosContabilidad;
	}

	/**
	 * Obtiene array con socios sin arrays
	 * Se obtienen los socios sin ahorros
	 * @return type
	 */
	public function obtenerSociosSinAhorros() {
		$sociosSinAhorros = ['A' => [], 'B' => [], 'C' => []];
		$sociosSinAhorrosRes = "SELECT ma.socio_id FROM socios.socios AS s LEFT JOIN ahorros.movimientos_ahorros AS ma ON ma.socio_id = s.id WHERE s.estado = 'ACTIVO' AND ma.entidad_id = ? AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND DATEADD(dd, DATEDIFF(dd, 0, ma.fecha_movimiento), 0) <= ? GROUP BY ma.socio_id HAVING SUM(valor_movimiento) = 0 UNION SELECT s.id FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id LEFT JOIN ahorros.movimientos_ahorros AS ma on ma.socio_id = s.id AND DATEADD(dd, DATEDIFF(dd, 0, ma.fecha_movimiento), 0) <= ? WHERE t.entidad_id = ? AND s.estado = 'ACTIVO' AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND ma.socio_id IS NULL";
		$sociosSinAhorrosRes = DB::select($sociosSinAhorrosRes, [$this->entidad->id, $this->fechaComparacion, $this->fechaComparacion, $this->fechaComparacion, $this->entidad->id, $this->fechaComparacion]);
		foreach($sociosSinAhorrosRes as $socio) {
			$socio = Socio::find($socio->socio_id);
			if(!empty($socio->fecha_afiliacion)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->fecha_afiliacion, false);
				if($diasDiferencia > 60) {
					array_push($sociosSinAhorros['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosSinAhorros['B'], $socio); 
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosSinAhorros['C'], $socio);
				}
			}
			else {
				array_push($sociosSinAhorros['A'], $socio);
			}
		}
		return $sociosSinAhorros;
	}

	/**
	 * Obtiene array con socios sin movimientos de ahorros por más de 30 días
	 * Se obtienen los socios sin movimientos de ahorros en más de 30 días
	 * @return type
	 */
	public function obtenerSociosSinMovimientosAhorrosEnTiempo() {
		$sociosSinMovimientosEnTiempo = ['A' => [], 'B' => [], 'C' => []];
		$sociosSinMovimientos = "WITH ultimoMovimiento AS (SELECT m.socio_id, m.fecha_movimiento, ROW_NUMBER() OVER (PARTITION BY socio_id ORDER BY fecha_movimiento DESC) AS rn FROM ahorros.movimientos_ahorros AS m WHERE m.entidad_id = ?) SELECT s.id, COALESCE(um.fecha_movimiento, s.fecha_afiliacion) as fecha_movimiento FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id left join ultimoMovimiento AS um ON um.socio_id = s.id AND um.rn = 1 WHERE t.entidad_id = ? AND s.estado = 'ACTIVO' AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ?";
		$sociosSinMovimientos = DB::select($sociosSinMovimientos, [$this->entidad->id, $this->entidad->id, $this->fechaComparacion]);
		foreach($sociosSinMovimientos as $socioSinMovimiento) {
			$socio = Socio::find($socioSinMovimiento->id);
			$fecha = "";
			$fecha = Carbon::createFromFormat('Y-m-d H:i:s.000', $socioSinMovimiento->fecha_movimiento)->startOfDay();
			$diasDiferencia = -$this->fechaComparacion->diffInDays($fecha, false);
			if($diasDiferencia > 60) {
				array_push($sociosSinMovimientosEnTiempo['B'], $socio);
			}
			elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
				array_push($sociosSinMovimientosEnTiempo['C'], $socio); 
			}
		}
		return $sociosSinMovimientosEnTiempo;
	}

	/**
	 * Obtiene array con socios con movimientos y sin movimientos en aportes
	 * Se obtienen socios sin aportes
	 * @return type
	 */
	public function obtenerSociosSinAportes() {
		$sociosSinAportes = ['A' => [], 'B' => [], 'C' => []];
		$sociosSinApo = "SELECT s.id FROM socios.socios AS s INNER JOIN general.terceros AS t ON s.tercero_id = t.id WHERE t.entidad_id = ? AND s.estado = 'ACTIVO' AND DATEADD(dd, DATEDIFF(dd, 0, s.fecha_afiliacion), 0) <= ? AND (SELECT COUNT(ma.id) FROM ahorros.movimientos_ahorros AS ma WHERE ma.socio_id = s.id AND ma.modalidad_ahorro_id = (SELECT TOP 1 moa.id FROM ahorros.modalidades_ahorros AS moa WHERE moa.entidad_id = ? AND moa.codigo = 'APO')) = 0";
		$sociosSinApo = DB::select($sociosSinApo, [$this->entidad->id, $this->fechaComparacion, $this->entidad->id]);
		foreach($sociosSinApo as $socioSinApo) {
			$socio = Socio::find($socioSinApo->id);
			array_push($sociosSinAportes['B'], $socio);
		}
		return $sociosSinAportes;
	}

	/**
	 * Obtiene array de socios con aportes en un límite del 10% por entidad
	 * Obtiene array de socios con aportes en un límite del 10% por entidad
	 * @return type
	 */
	public function obtenerSociosConAportesLimite() {
		$sociosAportesLimite = ['A' => [], 'B' => [], 'C' => []];
		$sociosAporteLim = "SELECT ma.socio_id, SUM(ma.valor_movimiento) AS saldo FROM ahorros.movimientos_ahorros AS ma WHERE ma.entidad_id = ? AND ma.modalidad_ahorro_id = (SELECT TOP 1 moa.id FROM ahorros.modalidades_ahorros AS moa WHERE moa.entidad_id = 4 AND moa.codigo = 'APO') AND DATEADD(dd, DATEDIFF(dd, 0, ma.fecha_movimiento), 0) <= ? GROUP BY ma.socio_id";
		$sociosAporteLim = DB::select($sociosAporteLim, [$this->entidad->id, $this->fechaComparacion]);
		$total = 0;
		foreach($sociosAporteLim as $aporte)$total += $aporte->saldo;
		$diezPorciento = (10 * $total) / 100;
		foreach($sociosAporteLim as $aporte) {
			if($aporte->saldo >= $diezPorciento) {
				$socio = Socio::find($aporte->id);
				array_push($sociosAportesLimite['B'], $socio);
			}
		}
		return $sociosAportesLimite;
	}

	/**
	 * Valida que en el caso no existan alertas tipo A
	 * @param type $caso 
	 * @return boolean
	 */
	private function tieneAlertasTipoA($caso) {
		return count($caso['A']) ? true : false;
	}

	/**
	 * Valida si el proceso se puede cerrar de acuerdo a la relas
	 * @return boolean
	 */
	public function validoParaCierre() {

		//Se valida que los casos de ahorros no tengan alertas tipo A
		if($this->tieneAlertasTipoA($this->obtenerAhorrosNegativos()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerSociosLiquidadosConAhorros()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerDiferenciaAhorrosContabilidad()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerSociosSinAhorros()))
			return false;

		return true;
	}
}