<?php

namespace App\Models\Creditos;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondicionModalidad extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.condiciones_modalidades";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'modalidad_id',
		'tipo_condicion', //MONTO, TASA, PLAZO
		'condicionado_por', //ANTIGUEDADEMPRESA, ANTIGUEDADENTIDAD, MONTO, PLAZO
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

	public function scopeModalidadId($query, $value) {
		if(!empty($value)) {
			$query->whereModalidadId($value);
		}
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('tipo_condicion', 'like', '%' . $value . '%')->orWhere('condicionado_por', 'like', '%' . $value . '%');
		}
	}
	
	/**
	 * Funciones
	 */

	public function contenidoEnCondicion($value) {
		$respuesta = $this->rangosCondicionesModalidad
							->where('condicionado_desde', '<=', $value)
							->where('condicionado_hasta', '>=', $value)
							->count();
							
		return $respuesta ? true : false;
	}

	public function valorCondicionado($value) {
		$respuesta = $this->rangosCondicionesModalidad
							->where('condicionado_desde', '<=', $value)
							->where('condicionado_hasta', '>=', $value)
							->first();
		
		return empty($respuesta) ? 0 : $respuesta->tipo_condicion_maximo;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function rangosCondicionesModalidad() {
		return $this->hasMany(RangoCondicionModalidad::class, 'condicion_modalidad_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function modalidad() {
		return $this->belongsTo(Modalidad::class, 'modalidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
