<?php

namespace App\Models\Sistema;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Menu extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "sistema.menus";

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
	
	public function getRutaAttribute() {
		return ($this->attributes['ruta'] == null)?'':$this->attributes['ruta'];
	}
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */
	
	public function scopePadres($query) {
		$query->whereNull('menu_padre_id');
	}
	
	/**
	 * Funciones
	 */
	
	public static function listar() {
		$menus = Menu::padres()
					->orderBy('orden')
					->with(['hijos' => function($query){
						$query->orderBy('orden');
					}])
					->get();

		$lista = array();

		foreach ($menus as $menu) {
			$lista[$menu->id] = $menu->nombre;

			foreach ($menu->hijos as $hijo) {
				$lista[$hijo->id] = $hijo->padre->nombre . " / " . $hijo->nombre;
			}
		}
		return $lista;
	}

	public static function listarTodos() {
		$menus = Menu::padres()
					->orderBy('orden')
					->with(['hijos' => function($query){
						$query->orderBy('orden')->with('hijos');
					}])
					->get();

		$lista = array();

		foreach ($menus as $menu) {
			$lista[$menu->id] = $menu->nombre;

			foreach ($menu->hijos as $hijo) {
				$lista[$hijo->id] = $hijo->padre->nombre . " / " . $hijo->nombre;

				foreach ($hijo->hijos as $subHijo) {
					$lista[$subHijo->id] = $subHijo->padre->padre->nombre . " / " . $subHijo->padre->nombre . " / " . $subHijo->nombre;
				}
			}
		}
		return $lista;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */
	
	public function hijos() {
		return $this->hasMany(Menu::class, 'menu_padre_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	public function padre() {
		return $this->belongsTo(Menu::class, 'menu_padre_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
	
	public function perfiles() {
		return $this->belongsToMany(Perfil::class, 'sistema.menu_perfil', 'menu_id', 'perfil_id')->withTimestamps();
	}
}
