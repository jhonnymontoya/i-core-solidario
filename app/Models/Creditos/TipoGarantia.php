<?php

namespace App\Models\Creditos;

use App\Models\Creditos\Modalidad;
use App\Models\General\Entidad;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoGarantia extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.tipos_garantia";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		//General
		'entidad_id',
		'codigo',
		'nombre',
		'descripcion',
		'tipo_garantia', //PERSONAL, REAL, FONDOGARANTIAS
		'es_permanente',
		'es_permanente_con_descubierto',
		'requiere_garantia_por_monto',
		'monto',
		'requiere_garantia_por_valor_descubierto',
		'valor_descubierto',

		//Codeudor
		'admite_codeudor_externo',
		'valida_cupo_codeudor',
		'tiene_limite_obligaciones_codeudor',
		'limite_obligaciones_codeudor',
		'tiene_limite_saldo_codeudas',
		'limite_saldo_codeudas',
		'valida_antiguedad_codeudor',
		'antiguedad_codeudor',
		'valida_calificacion_codeudor',
		'calificacion_minima_requerida_codeudor',

		'esta_activa',
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
		'es_permanente'									=> 'boolean',
		'es_permanente_con_descubierto'					=> 'boolean',
		'requiere_garantia_por_monto'					=> 'boolean',
		'requiere_garantia_por_valor_descubierto'		=> 'boolean',
		'admite_codeudor_externo'						=> 'boolean',
		'valida_cupo_codeudor'							=> 'boolean',
		'tiene_limite_obligaciones_codeudor'			=> 'boolean',
		'tiene_limite_saldo_codeudas'					=> 'boolean',
		'valida_antiguedad_codeudor'					=> 'boolean',
		'valida_calificacion_codeudor'					=> 'boolean',
		'esta_activa'									=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	public function getCondicionAttribute() {
		if($this->attributes['es_permanente']) {
			return 'Permanente';
		}
		elseif($this->attributes['es_permanente_con_descubierto']) {
			return 'Con descubierto';
		}
		elseif($this->attributes['requiere_garantia_por_monto']) {
			return 'Por monto';
		}
		elseif($this->attributes['requiere_garantia_por_valor_descubierto']) {
			return 'Por valor descubierto';
		}
		else {
			return '';
		}
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	public function setDescripcionAttribute($value) {
		if(!empty($value)) {
			$value = mb_convert_case($value, MB_CASE_LOWER, "UTF-8");
			$this->attributes['descripcion'] = ucfirst($value);
		}
		else {
			$this->attributes['descripcion'] = null;
		}
	}
	
	/**
	 * Scopes
	 */
	
	public function scopeEstado($query, $value = true) {
		return $query->whereEstaActiva($value);
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("codigo", "like", "%$value%")
				->orWhere("nombre", "like", "%$value%")
				->orWhere("descripcion", "like", "%$value%");
		}
	}

	public function scopeActiva($query, $value = true) {
		$query->whereEstaActiva($value);
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

	public function codeudores() {
		return $this->hasMany(Codeudor::class, 'tipo_garantia_id', 'id');
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

	public function modalidades() {
		return $this->belongsToMany(Modalidad::class, 'creditos.garantia_modalidad', 'tipo_garantia_id', 'modalidad_credito_id')->withTimestamps();
	}
}
