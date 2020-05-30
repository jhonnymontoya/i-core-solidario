<?php

namespace App\Models\General;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Indicador extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.indicadores";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'tipo_indicador_id',
		'fecha_inicio',
		'fecha_fin',
		'valor',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_inicio',
		'fecha_fin',
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

	public function setFechaInicioAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_inicio'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_inicio'] = null;
		}
	}

	public function setFechaFinAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_fin'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_fin'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeTipoIndicadorId($query, $value) {
		if(!empty($value)) {
			return $query->whereTipoIndicadorId($value);
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

	public function tipoIndicador() {
		return $this->belongsTo(TipoIndicador::class, 'tipo_indicador_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
