<?php

namespace App\Models\Sistema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogEvento extends Model
{
	use SoftDeletes;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "sistema.logs_eventos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'usuario_id',
		'usuario',
		'entidad_id',
		'tipo_evento',//INGRESAR, CONSULTAR, CREAR, ACTUALIZAR, ELIMINAR, SALIR
		'direccion',
		'user_agent',
		'verbo',
		'ruta',
		'descripcion',
		'modelo',
		'modelo_antes',
		'modelo_despues'
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

	const INGRESAR = 'INGRESAR';
	const CONSULTAR = 'CONSULTAR';
	const CREAR = 'CREAR';
	const ACTUALIZAR = 'ACTUALIZAR';
	const ELIMINAR = 'ELIMINAR';
	const SALIR = 'SALIR';

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
	 * Scope para seleccionar entidad
	 * @param type $query
	 * @param type|null $value
	 * @return type
	 */
	public function scopeEntidad($query, $value = null) {
		if(!empty($value)) {
			return $query->where("entidad_id", $value);
		}
	}

	/**
	 * Scope para seleccionar usuario
	 * @param type $query
	 * @param type|null $value
	 * @return type
	 */
	public function scopeUsusarioId($query, $value = null) {
		if(!empty($value)) {
			return $query->where("usuario_id", $value);
		}
	}

	/**
	 * Scope para seleccionar tipo de evento
	 * @param type $query
	 * @param type|null $value
	 * @return type
	 */
	public function scopetipoEvento($query, $value = null) {
		if(!empty($value)) {
			return $query->where("tipo_evento", $value);
		}
	}

	/**
	 * Scope para busquedas
	 * @param type $query
	 * @param type $value
	 * @return type
	 */
	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("usuario", "like", "%$value%")
				->orWhere("direccion", "like", "%$value%")
				->orWhere("ruta", "like", "%$value%")
				->orWhere("descripcion", "like", "%$value%")
				->orWhere("modelo", "like", "%$value%");
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

	/**
	 * Obtiene el Usuario asociado
	 * @return type|Usuario
	 */
	public function usuario() {
		return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
