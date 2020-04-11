<?php

namespace App\Models\ControlVigilancia;

use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;

class DetalleListaControl extends Model
{
	use FonadminTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "controlVigilancia.detalles_lista_control";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'lista_control_id',
		'tipo', //Individual,Entity
		'tipo_documento', 
		'numero_documento', 
		'primer_nombre', 
		'segundo_nombre', 
		'primer_apellido', 
		'segundo_apellido', 
		'es_colombiano', 
		'data' 
	];

	public $timestamps = false;


	/**
	 * Atributos que deben ser convertidos a tipos nativos.
	 *
	 * @var array
	 */
	protected $casts = [
		'es_colombiano' => 'boolean'
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
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */
	
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

	public function listaControl() {
		return $this->belongsTo(ListaControl::class, 'lista_control_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
