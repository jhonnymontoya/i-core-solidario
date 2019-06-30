<?php

namespace App\Models\Contabilidad;

use App\Models\General\ControlCierreModulo;
use App\Models\General\Entidad;
use App\Models\General\Reporte;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.modulos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'nombre',
		'esta_activo',
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
		'esta_activo' => 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */
	
	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = title_case($value);
	}

	/**
	 * Scopes
	 */
	
	public function scopeSearch($query, $value) {
		if(trim($value) != '') {
			$query->where('nombre', 'like', "%$value%");
		}
	}

	public function scopeActivo($query, $value = true) {
		if(trim($value) != '') {
			$query->where('esta_activo', $value);
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

	public function cuentasContables() {
		return $this->hasMany(Cuif::class, 'modulo_id', 'id');
	}

	public function tiposComprobantes() {
		return $this->hasMany(TipoComprobante::class, 'modulo_id', 'id');
	}

	public function controlCierresModulos() {
		return $this->hasMany(ControlCierreModulo::class, 'modulo_id', 'id');
	}

	public function reportes() {
		return $this->hasMany(Reporte::class, 'categoria_modulo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
