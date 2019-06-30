<?php

namespace App\Models\Sistema;

use App\Models\General\Entidad;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfil extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "sistema.perfiles";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'nombre',
		'descripcion',
		'esta_activo'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
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
		'esta_activo'	=> 'boolean',
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

	public function scopeActivo($query, $value = true) {
		if(trim($value) != '') {
			$query->whereEstaActivo($value);
		}
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('nombre', 'like', '%' . $value . '%')->orWhere('descripcion', 'like', '%' . $value . '%');
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		if(!empty($value)) {
			$query->whereEntidadId($value);
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
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
	
	public function menus() {
		return $this->belongsToMany(Menu::class, 'sistema.menu_perfil', 'perfil_id', 'menu_id')->withTimestamps();
	}

	public function usuarios() {
		return $this->belongsToMany(Usuario::class, 'sistema.usuario_perfil', 'perfil_id', 'usuario_id')->withTimestamps();
	}
}
