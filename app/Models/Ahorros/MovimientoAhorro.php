<?php

namespace App\Models\Ahorros;

use App\Models\Contabilidad\Movimiento;
use App\Models\General\Entidad;
use App\Models\Socios\Socio;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoAhorro extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

    /**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.movimientos_ahorros";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'socio_id',
		'modalidad_ahorro_id',
		'movimiento_id',
		'fecha_movimiento',
		'valor_movimiento',
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

	public function scopeSocioId($query, $value) {
		if(!empty($value)) {
			$query->whereSocioId($value);
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

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}

	public function movimiento() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}

	public function modalidadAhorro() {
		return $this->belongsTo(ModalidadAhorro::class, 'modalidad_ahorro_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
