<?php

namespace App\Models\General;

use App\Models\Contabilidad\Modulo;
use App\Models\Creditos\CierreCartera;
use App\Models\Sistema\Usuario;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlCierreModulo extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.control_cierre_modulos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'control_periodo_cierre_id',
		'modulo_id',
		'usuario_id',
		'fecha_cierre',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
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
	 * Scope para seleccionar por entidad
	 * @param type $query
	 * @param type $value
	 * @return type
	 */
	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	/**
	 * Scope para selecionar por moduloId
	 * @param type $query
	 * @param type $value
	 * @return type
	 */
	public function scopeModuloId($query, $value) {
		if(!empty($value)) {
			$query->whereModuloId($value);
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

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'control_cierre_modulo_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function modulo() {
		return $this->belongsTo(Modulo::class, 'modulo_id', 'id');
	}

	public function controlProcesoCierre() {
		return $this->belongsTo(ControlPeriodoCierre::class, 'control_periodo_cierre_id', 'id');
	}

	public function usuario() {
		return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
