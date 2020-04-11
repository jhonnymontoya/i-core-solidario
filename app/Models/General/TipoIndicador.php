<?php

namespace App\Models\General;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoIndicador extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.tipos_indicadores";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'codigo',
		'descripcion',
		'periodicidad',
		'variable'
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

	public function getEstaActualizadoAttribute() {
		$actualizado = false;
		if($this->indicadores->count()) {
			$indicador = Indicador::tipoIndicadorId($this->id)
									->where('fecha_inicio', '<=', Carbon::now()->startOfDay())
									->where('fecha_fin', '>=', Carbon::now()->endOfDay())
									->get();
			if($indicador->count())$actualizado = true;
		}

		return $actualizado;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Scopes
	 */

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("codigo", "like", "%$value%")
						->orWhere("descripcion", "like", "%$value%");
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->whereEntidadId($value);
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

	public function indicadores() {
		return $this->hasMany(Indicador::class, 'tipo_indicador_id', 'id');
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
