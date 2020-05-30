<?php

namespace App\Models\General;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pais extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.paises";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'codigo',
		'prefijo',
		'nombre',
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

	public function setPrefijoAttribute($value) {
		$this->attributes['prefijo'] = mb_strtoupper($value);
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
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

	public function departamentos() {
		return $this->hasMany(Departamento::class, 'pais_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
