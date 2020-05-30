<?php

namespace App\Models\Creditos;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RangoCondicionModalidad extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.condiciones_modalidades_rangos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'condicion_modalidad_id',
		'condicionado_desde',
		'condicionado_hasta',
		'tipo_condicion_minimo',
		'tipo_condicion_maximo',
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
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */

	public function scopeCondicionModalidadId($query, $value) {
		if(!empty($value)) {
			$query->whereCondicionModalidadId($value);
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

	public function condicionModalidad() {
		return $this->belongsTo(CondicionModalidad::class, 'condicion_modalidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
