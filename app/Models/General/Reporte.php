<?php

namespace App\Models\General;

use App\Models\Contabilidad\Modulo;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.reportes";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'categoria_modulo_id',
		'nombre',
		'descripcion',
		'ruta_reporte',
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
		'esta_activo' 	=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

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
	
	public function scopeActivo($query, $value = true) {
		return $query->where('esta_activo', $value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("nombre", "like", "%$value%")
				->orWhere("descripcion", "like", "%$value%");
		}
	}
	
	public function scopeCategoria($query, $value) {
		if(!empty($value)) {
			return $query->whereCategoriaModuloId($value);
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

	public function parametros() {
		return $this->hasMany(ParametroReporte::class, 'reporte_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function modulo() {
		return $this->belongsTo(Modulo::class, 'categoria_modulo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
