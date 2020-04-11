<?php

namespace App\Models\Creditos;

use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeguroCartera extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.seguros_cartera";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'aseguradora_tercero_id',
		'codigo',
		'nombre',
		'base_prima', //SALDO, VALORINICIAL
		'tasa_mes',
		'esta_activo'
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
		'esta_activo'				=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Scopes
	 */
	
	public function scopeActivo($query, $value = true) {
		return $query->whereEstaActivo($value);
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("codigo", "like", "%$value%")
					->orWhere("nombre", "like", "%$value%");
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

	public function solicitudesCreditos() {
		return $this->hasMany(SolicitudCredito::class, 'seguro_cartera_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function aseguradoraTercero() {
		return $this->belongsTo(Tercero::class, 'aseguradora_tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function modalidades() {
		return $this->belongsToMany(Modalidad::class, 'creditos.modalidades_por_seguro_cartera', 'seguro_cartera_id', 'modalidad_credito_id')
					->withTimestamps();
	}
}
