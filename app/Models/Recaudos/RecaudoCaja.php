<?php

namespace App\Models\Recaudos;

use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\Movimiento;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RecaudoCaja extends Model
{
	use ICoreTrait, ICoreModelTrait;
    /**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.recaudos_caja";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tercero_id',
		'recaudo_cuif_id',
		'movimiento_id',
		'fecha_recaudo',
		'recaudo'
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_recaudo',
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

	public function setFechaRecaudoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_recaudo'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_recaudo'] = null;
		}
	}
		
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("nombre", "like", "%$value%");
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

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function cuif() {
		return $this->belongsTo(Cuif::class, 'recaudo_cuif_id', 'id');
	}

	public function movimiento() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
