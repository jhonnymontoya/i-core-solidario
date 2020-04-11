<?php

namespace App\CierreCartera;

use Illuminate\Support\Facades\DB;
use App\Models\General\Entidad;
use Carbon\Carbon;
use Exception;

class Contabilidad
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
	 * Obtiene array con comprobantes descuadrados
	 * Se obtienen los comprobantes descuadrados (A)
	 * @return array
	 */
	public function obtenerComprobantesDescuadrados() {
		$comprobantesDescuadrados = ['A' => [], 'B' => [], 'C' => []];

		$sql = "select movimiento_id, codigo_comprobante, numero_comprobante, sum(debito) as debitos, sum(credito) as creditos, sum(debito) - sum(credito) as diferencia from contabilidad.detalle_movimientos where entidad_id = ? group by movimiento_id, codigo_comprobante, numero_comprobante having sum(debito) - sum(credito) <> 0";
		$res = DB::select($sql, [$this->entidad->id]);
		if($res) {
			foreach ($res as $item) {
				array_push($comprobantesDescuadrados['A'], $item);
			}
		}
		return $comprobantesDescuadrados;
	}

	/**
	 * Obtiene array con comprobantes con impuestos
	 * Se obtienen comprobantes con impuestos en estado borrador (B)
	 * @return array
	 */
	public function obtenerComprobantesBorradorConImpuesto() {
		$comprobantesConImpuestos = ['A' => [], 'B' => [], 'C' => []];
		$sql = "SELECT CONCAT(tc.codigo, '-', tc.nombre) AS codigo_comprobante, mt.fecha_movimiento AS fecha, mt.descripcion FROM contabilidad.movimientos_temporal AS mt INNER JOIN contabilidad.movimiento_impuesto_temporal AS mit ON mit.movimiento_termporal_id = mt.id INNER JOIN contabilidad.tipos_comprobantes AS tc ON mt.tipo_comprobante_id = tc.id WHERE mt.entidad_id = ?";
		$res = DB::select($sql, [$this->entidad->id]);
		if($res) {
			foreach ($res as $item) {
				array_push($comprobantesConImpuestos['B'], $item);
			}
		}
		return $comprobantesConImpuestos;
	}

	/**
	 * Obtiene array con comprobantes en borrador
	 * Se obtienen comprobantes en borrador (C)
	 * @return array
	 */
	public function obtenerComprobantesBorrador() {
		$comprobantesBorrador = ['A' => [], 'B' => [], 'C' => []];
		$sql = "SELECT CONCAT(tc.codigo, '-', tc.nombre) AS codigo_comprobante, mt.fecha_movimiento AS fecha, mt.descripcion FROM contabilidad.movimientos_temporal AS mt INNER JOIN contabilidad.tipos_comprobantes AS tc ON mt.tipo_comprobante_id = tc.id WHERE mt.entidad_id = ?";
		$res = DB::select($sql, [$this->entidad->id]);
		if($res) {
			foreach ($res as $item) {
				array_push($comprobantesBorrador['C'], $item);
			}
		}
		return $comprobantesBorrador;
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

		//Se valida que los casos de socios no tengan alertas tipo A
		if($this->tieneAlertasTipoA($this->obtenerComprobantesDescuadrados()))
			return false;

		return true;
	}
}