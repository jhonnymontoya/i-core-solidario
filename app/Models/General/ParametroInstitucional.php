<?php

namespace App\Models\General;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParametroInstitucional extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

    /**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.parametros_institucionales";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'modulo',
		'codigo',
		'descripcion',
		'valor',
		'indicador',
		'tipo_indicador',
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
		'indicador' 				=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	public function getValorAttribute() {
		return floatval($this->attributes['valor']);
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

	public function scopeCodigo($query, $value) {
		if(!empty($value)) {
			$query->where('codigo', $value);
		}
	}	

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('codigo', 'like', '%' . $value . '%')->orWhere('descripcion', 'like', '%' . $value . '%');
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
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
