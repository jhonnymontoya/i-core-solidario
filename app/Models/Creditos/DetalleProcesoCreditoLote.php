<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetalleProcesoCreditoLote extends Model
{

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.detalles_proceso_credito_lote";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'proceso_credito_lote_id',
		'detalle',
		'condiciones',
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

	public function getSolicitudCreditoAttribute() {
		$solicitudCredito = new SolicitudCredito;
		$obj = json_decode($this->detalle, true);
		if(!empty($obj['fecha_solicitud'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_solicitud'], 0, 10))->startOfDay();
			unset($obj['fecha_solicitud']);
			$solicitudCredito->fecha_solicitud = $fecha;
		}
		if(!empty($obj['fecha_aprobacion'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_aprobacion'], 0, 10))->startOfDay();
			unset($obj['fecha_aprobacion']);
			$solicitudCredito->fecha_aprobacion = $fecha;
		}
		if(!empty($obj['fecha_desembolso'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_desembolso'], 0, 10))->startOfDay();
			unset($obj['fecha_desembolso']);
			$solicitudCredito->fecha_desembolso = $fecha;
		}
		if(!empty($obj['fecha_cancelacion'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_cancelacion'], 0, 10))->startOfDay();
			unset($obj['fecha_cancelacion']);
			$solicitudCredito->fecha_cancelacion = $fecha;
		}
		if(!empty($obj['fecha_primer_pago'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_primer_pago'], 0, 10))->startOfDay();
			unset($obj['fecha_primer_pago']);
			$solicitudCredito->fecha_primer_pago = $fecha;
		}
		if(!empty($obj['fecha_primer_pago_intereses'])) {
			$fecha = Carbon::createFromFormat('Y-m-d', substr($obj['fecha_primer_pago_intereses'], 0, 10))->startOfDay();
			unset($obj['fecha_primer_pago_intereses']);
			$solicitudCredito->fecha_primer_pago_intereses = $fecha;
		}
		$solicitudCredito->fill($obj);
		return $solicitudCredito;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setSolicitudCreditoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['detalle'] = $value->toJson();
		}
		else {
			$this->attributes['detalle'] = '';
		}
	}
	
	/**
	 * Scopes
	 */
	
	/**
	 * Funciones
	 */
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relaciones Muchos a uno
	 */

	public function procesoCreditoLote() {
		return $this->belongsTo(ProcesoCreditosLote::class, 'proceso_credito_lote_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
