<?php

namespace App\Models\General;

use App\Models\Contabilidad\DetalleMovimiento;
use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependencia extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.dependencias";

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
		'fecha_apertura',
		'fecha_cierre',
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
	
	/**
	 * Setters Personalizados
	 */
	
	public function setFechaAperturaAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_apertura'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_apertura'] = null;
		}
	}
	
	public function setFechaCierreAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_cierre'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_cierre'] = null;
		}
	}
	
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
	
	public function detallesMovimientos() {
		return $this->hasMany(DetalleMovimiento::class, 'dependencia_id', 'id');
	}

	public function socios() {
		return $this->hasMany(Socio::class, 'dependencia_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function ciudad() {
		return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id');
	}

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
