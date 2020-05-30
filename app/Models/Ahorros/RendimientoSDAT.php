<?php

namespace App\Models\Ahorros;

use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class RendimientoSDAT extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.rendimientos_sdat";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'socio_id',
		'sdat_id',
		'movimiento_id',
		'valor',
		'fecha_movimiento',
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
		'valor'					=> 'float',
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

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
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

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function SDAT() {
		return $this->belongsTo(SDAT::class, 'sdat_id', 'id');
	}

	public function movimiento() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
