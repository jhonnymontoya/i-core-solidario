<?php

namespace App\Models\General;

use App\Models\Sistema\Usuario;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoIdentificacion extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.tipos_identificacion";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'aplicacion',
		'codigo',
		'nombre',
		'esta_activo',
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
		'esta_activo' => 'boolean',
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
		$this->attributes['nombre'] = title_case($value);
	}
	
	/**
	 * Scopes
	 */

	public function scopeSearch($query, $value) {
		if(trim($value) != '') {
			$query->where('codigo', 'like', "%$value%")
					->orWhere('nombre', 'like', "%$value%");
		}
	}

	public function scopeActivo($query, $value = true) {
		if(trim($value) != '') {
			$query->where('esta_activo', $value);
		}
	}

	public function scopeAplicacion($query, $value) {
		if(trim($value) != '') {
			$query->where('aplicacion', $value);
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

	public function terceros() {
		return $this->hasMany(Tercero::class, 'tipo_identificacion_id', 'id');
	}

	public function usuarios() {
		return $this->hasMany(Usuario::class, 'tipo_identificacion_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
