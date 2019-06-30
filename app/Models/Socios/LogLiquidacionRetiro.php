<?php

namespace App\Models\Socios;

use Illuminate\Database\Eloquent\Model;

class LogLiquidacionRetiro extends Model
{
	public $timestamps = false;

	protected $table = "socios.log_liquidacion_retiros";

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

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
