<?php

namespace App\Models\Ahorros;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondicionSDAT extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.condiciones_sdat";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tipo_sdat_id',
		'plazo_minimo',
		'plazo_maximo',
		'monto_minimo',
		'monto_maximo',
		'tasa',
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
		'plazo_minimo' => 'integer',
		'plazo_maximo' => 'integer',
		'monto_minimo' => 'float',
		'monto_maximo' => 'float',
		'tasa' => 'float'
	];

	/**
	 * Getters personalizados
	 */

	public function getPeriodoAttribute() {
		$per = sprintf("%s - %s días", $this->attributes["plazo_minimo"], $this->attributes["plazo_maximo"]);
		return $per;
	}
	
	/**
	 * Setters Personalizados
	 */
	
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

	public function tipoSDAT() {
		return $this->belongsTo(TipoSDAT::class, 'tipo_sdat_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
