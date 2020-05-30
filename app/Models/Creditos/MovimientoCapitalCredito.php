<?php

namespace App\Models\Creditos;

use App\Models\Contabilidad\Movimiento;
use App\Models\Tarjeta\LogMovimientoTransaccionEnviado;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCapitalCredito extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.movimientos_capital_credito";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'solicitud_credito_id',
		'movimiento_id',
		'fecha_movimiento',
		'valor_movimiento',
		'origen'
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_movimiento',
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
		'aplica_mora'				=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaMovimientoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_movimiento'] = null;
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

	public function logMovimientosTransaccionesEnviados()
	{
		return $this->hasMany(
			LogMovimientoTransaccionEnviado::class,
			'movimiento_capital_credito_id',
			'id'
		);
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function solicitudCredito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}

	public function movimientoContable() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
