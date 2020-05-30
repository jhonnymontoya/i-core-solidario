<?php

namespace App\Models\Creditos;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObligacionConsolidacion extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.obligaciones_consolidacion";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'solicitud_credito_id',
		'solicitud_credito_consolidado_id',
		'pago_capital',
		'pago_intereses',
		'tipo_consolidacion', //SALDOTOTAL, INCLUIDORECAUDO, PARCIAL
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
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

	public function getTotalAttribute() {
		return $this->pago_capital + $this->pago_intereses;
	}

	/**
	 * Setters Personalizados
	 */

	/**
	 * Scopes
	 */

	public function scopeCreditoConsolidado($query, $value) {
		if(!empty($value)) {
			return $query->where('solicitud_credito_consolidado_id', $value);
		}
	}

	public function scopeCreditoQueConsolida($query, $value) {
		if(!empty($value)) {
			return $query->where('solicitud_credito_id', $value);
		}
	}

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

	public function credito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}

	public function creditoConsolidado() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_consolidado_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
