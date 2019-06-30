<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sistema\Usuario;

class CumplimientoCondicion extends Model
{
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.cumplimiento_condiciones";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'solicitud_id',
		'condicion',
		'valor_parametro',
		'valor_solicitud',
		'cumple_parametro',
		'es_aprobada',
		'aprobado_por_usuario_id',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
	];

	/**
	 * Atributos que deben ser convertidos a tipos nativos.
	 *
	 * @var array
	 */
	protected $casts = [
		'cumple_parametro'			=> 'boolean',
		'es_aprobada'				=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	public function getCumpleAttribute() {
		return $this->cumple_parametro | $this->es_aprobada;
	}

	public function getAprobadoPorAttribute() {
		return $this->usuario;
	}
	
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

	public function solicitud() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_id', 'id');
	}

	public function usuario() {
		return $this->belongsTo(Usuario::class, 'aprobado_por_usuario_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
