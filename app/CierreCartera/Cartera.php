<?php

namespace App\CierreCartera;

use App\Models\Creditos\SolicitudCredito;
use Illuminate\Support\Facades\DB;
use App\Models\General\Entidad;
use Carbon\Carbon;
use Exception;

class Cartera
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
	 * Obtiene array con los créditos con días de vencimiento entre de 30 y 60
	 * Se obtienen los créditos con días de vencimiento entre de 30 y 60 (B, C)
	 * @return array
	 */
	public function obtenerCarteraDiasVencido() {
		$carteraDiasVencidos = ['A' => [], 'B' => [], 'C' => []];

		$res = DB::select("exec general.sp_cartera_dias_vencidos ?, ?", [$this->entidad->id, $this->fechaComparacion]);
		if($res) {
			foreach ($res as $item) {
				if($item->diasVencidos > 90) array_push($carteraDiasVencidos['B'], $item);
				else array_push($carteraDiasVencidos['C'], $item);
			}
		}
		return $carteraDiasVencidos;
	}

	/**
	 * Obtiene array con los créditos con estados iguales a borrador, radicado y aprobado
	 * Se obtiene los creditos sin definir (C)
	 * @return array
	 */
	public function obtenerCreditosSinDefinir() {
		$creditosSinDefinir = ['A' => [], 'B' => [], 'C' => []];
		$solicitudes = SolicitudCredito::entidadId()
			->where('fecha_solicitud', '<', $this->fechaComparacion)
			->whereIn('estado_solicitud', array('APROBADO', 'RADICADO', 'BORRADOR'))
			->get();
		$creditosSinDefinir['C'] = $solicitudes->all();
		return $creditosSinDefinir;
	}

	/**
	 * Obtiene array con los créditos con saldo negativo
	 * Se obtiene los créditos con saldo negativo (A)
	 * @return array
	 */
	public function obtenerCarteraSaldoNegativo() {
		$carteraSaldoNegativo = ['A' => [], 'B' => [], 'C' => []];

		$res = DB::select("exec general.sp_cartera_saldo_negativo ?, ?", [$this->entidad->id, $this->fechaComparacion]);
		if($res) {
			foreach ($res as $item) {
				array_push($carteraSaldoNegativo['A'], $item);
			}
		}
		return $carteraSaldoNegativo;
	}

	/**
	 * Obtiene array con los créditos sin amortización
	 * Se obtiene los créditos sin amortización (A)
	 * @return array
	 */
	public function obtenerCarteraSinAmortizacion() {
		$carteraSaldoNegativo = ['A' => [], 'B' => [], 'C' => []];

		$query = "SELECT * FROM creditos.solicitudes_creditos AS sc LEFT JOIN creditos.amortizaciones AS a ON a.obligacion_id = sc.id WHERE sc.entidad_id = ? AND sc.fecha_desembolso <= ? AND sc.estado_solicitud = 'DESEMBOLSADO' AND a.obligacion_id IS NULL";
		$res = DB::select($query, [$this->entidad->id, $this->fechaComparacion]);
		if($res) {
			foreach ($res as $item) {
				array_push($carteraSaldoNegativo['A'], $item);
			}
		}
		return $carteraSaldoNegativo;
	}

	/**
	 * Obtiene array con los créditos con saldo y estado diferente a desembolsado
	 * Se obtiene los creditos con saldo y estado diferente a desembolsado (A)
	 * @return array
	 */
	public function obtenerCarteraConSaldoEstadoDiferenteDesembolsado() {
		$carteraSaldo = ['A' => [], 'B' => [], 'C' => []];
		$res = DB::select("exec general.sp_cartera_con_saldo_diferente_desembolso ?, ?", [$this->entidad->id, $this->fechaComparacion]);
		if($res) {
			foreach ($res as $item) {
				array_push($carteraSaldo['A'], $item);
			}
		}
		return $carteraSaldo;
	}

	/**
	 * Obtiene array con Se obtiene la diferencia entre cartera y contabilidad
	 * Se obtiene la diferencia entre cartera y contabilidad (A)
	 * @return array
	 */
	public function obtenerDiferenciaCarteraContabilidad() {
		$carteraSaldo = ['A' => [], 'B' => [], 'C' => []];

		$queryCartera = "SELECT SUM(mcc.valor_movimiento) sumaCreditos FROM creditos.movimientos_capital_credito AS mcc INNER JOIN creditos.solicitudes_creditos AS sc ON mcc.solicitud_credito_id = sc.id WHERE sc.entidad_id = ? AND general.fn_fecha_sin_hora(mcc.fecha_movimiento) <= ?";
		$queryContabilidad = "SELECT SUM(dm.debito) - SUM(dm.credito) AS contabilidad FROM contabilidad.detalle_movimientos AS dm INNER JOIN contabilidad.movimientos AS m ON dm.movimiento_id = m.id WHERE dm.entidad_id =? AND dm.cuif_id IN(SELECT DISTINCT cuif_capital_id FROM creditos.parametros_contables WHERE entidad_id = ?) AND general.fn_fecha_sin_hora(dm.fecha_movimiento) <= ? AND m.causa_anulado_id IS NULL";

		$resCartera = DB::select($queryCartera, [$this->entidad->id, $this->fechaComparacion]);
		$resContabilidad = DB::select($queryContabilidad, [$this->entidad->id, $this->entidad->id, $this->fechaComparacion]);

		if($resCartera && $resContabilidad) {
			$resCartera = floatval($resCartera[0]->sumaCreditos);
			$resContabilidad = floatval($resContabilidad[0]->contabilidad);
			if($resCartera != $resContabilidad) {
				array_push($carteraSaldo['A'], $resCartera - $resContabilidad);
			}
		}
		return $carteraSaldo;
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

		//Se valida que los casos de cartera no tengan alertas tipo A
		if($this->tieneAlertasTipoA($this->obtenerCarteraSaldoNegativo()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerCarteraSinAmortizacion()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerCarteraConSaldoEstadoDiferenteDesembolsado()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerDiferenciaCarteraContabilidad()))
			return false;

		return true;
	}
}