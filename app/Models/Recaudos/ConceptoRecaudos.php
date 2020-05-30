<?php

namespace App\Models\Recaudos;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\Modalidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;

class ConceptoRecaudos extends Model
{
	use ICoreTrait, ICoreModelTrait;
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.conceptos_recaudos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'pagaduria_id',
		'codigo',
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

	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
		
	/**
	 * Scopes
	 */

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

	public function recaudosNomina() {
		return $this->hasMany(RecaudoNomina::class, 'concepto_recaudo_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function pagaduria() {
		return $this->belongsTo(Pagaduria::class, 'pagaduria_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function modalidadesAhorros() {
		return $this->belongsToMany(ModalidadAhorro::class, 'recaudos.conceptos_modalidades_ahorros', 'concepto_id', 'modalidad_ahorro_id')->withTimestamps();
	}

	public function modalidadesCreditos() {
		return $this->belongsToMany(Modalidad::class, 'recaudos.conceptos_modalidades_creditos', 'concepto_id', 'modalidad_credito_id')->withTimestamps();
	}
}
