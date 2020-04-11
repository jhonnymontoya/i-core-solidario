<?php

namespace App\Models\ControlVigilancia;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ListaControl extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "controlVigilancia.listas_control";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'nombre',
		'tipo', //INTERNACIONAL, NACIONAL
		'fecha_publicacion' 
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_publicacion',
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
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	/**
	 * Getters personalizados
	 */

	public function getCantidadDetallesAttribute() {
		return $this->detallesListaControl->count();
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaPublicacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_publicacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_publicacion'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->whereNombre($value);
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

	public function detallesListaControl() {
		return $this->hasMany(DetalleListaControl::class, 'lista_control_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
