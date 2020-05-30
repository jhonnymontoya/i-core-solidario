<?php

namespace App\Models\General;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organismo extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.organismos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'tipo_organo',
		'calidad',
		'fecha_nombramiento',
		'periodos',
		'tarjeta_profesional',
		'revisoria_fiscal_es_empresa',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_nombramiento',
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
		'revisoria_fiscal_es_empresa' => 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */
	
	public function setFechaNombramientoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_nombramiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_nombramiento'] = null;
		}
	}
	/**
	 * Scopes
	 */
	
	public function scopeEntidad($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->whereHas('tercero', function($q) use($value){
			$q->where('entidad_id', $value);
		});
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

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
