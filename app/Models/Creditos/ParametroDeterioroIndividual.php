<?php

namespace App\Models\Creditos;

use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParametroDeterioroIndividual extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.parametros_deterioro_individual";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tipo_cartera', //CONSUMO, VIVIENDA, COMERCIAL, MICROCREDITO
		'clase', //CAPITAL, INTERES
		'dias_desde',
		'dias_hasta',
		'deterioro'
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
		'dias_desde'	=> 'integer',
		'dias_hasta'	=> 'integer',
		'deterioro'		=> 'float'
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

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeTipoCartera($query, $value) {
		if(!empty($value)) {
			$query->whereTipoCartera($value);
		}
	}

	public function scopeClase($query, $value) {
		if(!empty($value)) {
			$query->whereClase($value);
		}
	}
	
	/**
	 * Funciones
	 */
	public static function contenidoEnParametro($tipoCartera, $clase, $valor) {
		$respuesta = ParametroDeterioroIndividual::entidadId()
							->whereTipoCartera($tipoCartera)
							->whereClase($clase)
							->where('dias_desde', '<=', $valor)
							->where('dias_hasta', '>=', $valor)
							->count();
		return $respuesta ? true : false;
	}
	 
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
