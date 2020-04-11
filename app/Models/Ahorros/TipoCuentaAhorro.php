<?php

namespace App\Models\Ahorros;

use App\Models\Contabilidad\Cuif;
use App\Models\General\Entidad;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCuentaAhorro extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.tipos_cuentas_ahorros";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'capital_cuif_id',
		'nombre_producto',
		'saldo_minimo',
		'dias_para_inactivacion',
		'esta_activa'
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
		'esta_activa'				=> 'boolean',
		'saldo_minimo'				=> 'float',
		'dias_para_inactivacion'	=> 'integer'
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setNombreProductoAttribute($value) {
		$this->attributes['nombre_producto'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("nombre_producto", "like", "%$value%");
		}
	}

	public function scopeEstaActiva($query, $value = true) {
		if(!is_null($value)) {
			return $query->whereEstaActiva($value);
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
	 * Relación uno a muchos para cuentas de ahorros
	 * @return Colección CuentaAhorro
	 */
	public function cuentasAhorros() {
		return $this->hasMany(CuentaAhorro::class, 'tipo_cuenta_ahorro_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	/**
	 * Relación muchos a uno para la entidad a la que pertenece
	 * @return Entidad
	 */
	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	/**
	 * Relación muchos a uno para la cuenta de capital
	 * @return Cuif
	 */
	public function capitalCuif() {
		return $this->belongsTo(Cuif::class, 'capital_cuif_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
