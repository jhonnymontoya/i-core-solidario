<?php

namespace App\Models\General;

use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ciudad extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.ciudades";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
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

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = Str::title($value);
	}
	
	/**
	 * Scopes
	 */

	public function scopeSearch($query, $valor) {
		if(!empty($valor)) {
			$query->where("nombre", "like", "%$valor%");
		}
	}

	public function scopeId($query, $valor) {
		if(!empty($valor)) {
			$query->where('id', $valor);
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
	
	public function contactos() {
		return $this->hasMany(Contacto::class, 'ciudad_id', 'id');
	}

	public function dependencias() {
		return $this->hasMany(Dependencia::class, 'ciudad_id', 'id');
	}

	public function tercerosCiudadExpedicion() {
		return $this->hasMany(Tercero::class, 'ciudad_expedicion_documento_id', 'id');
	}

	public function tercerosCiudadConstitucion() {
		return $this->hasMany(Tercero::class, 'ciudad_constitucion_id', 'id');
	}

	public function tercerosCiudadNacimiento() {
		return $this->hasMany(Tercero::class, 'ciudad_nacimiento_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */
	
	public function departamento() {
		return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
