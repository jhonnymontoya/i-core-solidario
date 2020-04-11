<?php

namespace App\Models\Creditos;

use App\Models\Creditos\SolicitudCredito;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetalleAjusteCreditoLote extends Model
{
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.detalles_ajustes_creditos_lote";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'ajuste_credito_lote_id',
		'detalle',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
	];

	/**
	 * Atributos que deben ser convertidos a tipos nativos.
	 *
	 * @var array
	 */
	protected $casts = [
	];

	/**
	 * Getters personalizados
	 */

	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */
	
	/**
	 * Funciones
	 */

	public function getSolicitudCredito() {
		$arr = json_decode($this->detalle);
		$solicitudCredito = SolicitudCredito::find($arr->solicitud_credito_id);
		return empty($solicitudCredito) ? false : $solicitudCredito;
	}

	public function getTercero() {
		$solicitudCredito = $this->getSolicitudCredito();
		$tercero = optional($solicitudCredito)->tercero;
		return empty($tercero) ? false : $tercero;
	}

	public function getModalidad() {
		$solicitudCredito = $this->getSolicitudCredito();
		$modalidadCredito = optional($solicitudCredito)->modalidadCredito;
		return empty($modalidadCredito) ? false : $modalidadCredito;
	}

	public function getValorCapital() {
		$arr = json_decode($this->detalle);
		return $arr->valor_capital;
	}

	public function getValorIntereses() {
		$arr = json_decode($this->detalle);
		return $arr->valor_intereses;
	}

	public function getValorSeguro() {
		$arr = json_decode($this->detalle);
		return $arr->valor_seguro;
	}

	public function getValorTotal() {
		$arr = json_decode($this->detalle);
		return $this->getValorCapital() + $this->getValorIntereses() + $this->getValorSeguro();
	}

	public function getSaldoObligacion($fechaConsulta = null) {
		$fecha = $fechaConsulta == null ? $this->ajusteCreditoLote->fecha_proceso : Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$solicitudCredito = $this->getSolicitudCredito();
		if(!$solicitudCredito)return 0;
		return $solicitudCredito->saldoObligacion($fecha);
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relaciones Muchos a uno
	 */

	public function ajusteCreditoLote() {
		return $this->belongsTo(AjusteCreditoLote::class, 'ajuste_credito_lote_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
