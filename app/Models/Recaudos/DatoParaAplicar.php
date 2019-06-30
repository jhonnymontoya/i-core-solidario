<?php

namespace App\Models\Recaudos;

use Illuminate\Database\Eloquent\Model;
use App\Models\General\Tercero;

class DatoParaAplicar extends Model
{
	public $timestamps = false;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.datos_para_aplicar";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'control_proceso_id',
		'tercero_id',
		'valor_descontado',
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

	public function controlProceso() {
		return $this->belongsTo(ControlProceso::class, 'control_proceso_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
