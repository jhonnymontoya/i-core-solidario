<?php

namespace App\Models\Tesoreria;

use App\Models\General\Entidad;
use App\Models\Socios\TarjetaCredito;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Franquicia extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "tesoreria.franquicias";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
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
	
	/**
	 * Filtra las franquicias por el id de la entidad
	 * @param  [type] $query [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
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

	public function tarjetasCredito() {
		return $this->hasMany(TarjetaCredito::class, 'franquicia_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
