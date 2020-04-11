<?php

namespace App\Models\General;

use App\Models\Contabilidad\Modulo;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlPeriodoCierre extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.control_periodos_cierres";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'mes',
		'anio',
		'fecha_apertura',
		'fecha_cierre',
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
	 * Funciones
	 */

	/**
	 * Obtiene el periodo anterior, si este no existe,
	 * Es el primer periodo
	 * @param type $obj
	 * @return variant
	 */
	private function obtenerPeriodoAnterior() {
		$periodo = ControlPeriodoCierre::entidadId()
			->where('id', '<', $this->id)
			->orderBy('anio', 'desc')
			->orderBy('mes', 'desc')
			->first();
		return $periodo;
	}

	/**
	 * Valida si un modulo se encuentra cerrado para el proceso actual
	 * @param type $idModulo 
	 * @return boolean
	 */
	public function moduloCerrado($idModulo) {
		if($this->controlCierresModulos()->whereModuloId($idModulo)->count()) {
			return true;
		}
		else {
			return false;
		}
	}

	public function moduloSePuedeCerrar($idModulo) {
		$ordenModulos = $this->entidad->getModulos()->reverse();
		$indiceModulo = null;

		//Se búsca el índice del modulo
		foreach ($ordenModulos as $key => $modulo) {
			if($modulo->id == $idModulo) {
				$indiceModulo = $key;
				break;
			}
		}

		//Se verifica si el módulo ya ha sido cerrado
		if($this->moduloCerrado($idModulo)) {
			return false;
		}

		if($indiceModulo > 0) {
			//se valida el modulo inmediatamente anterior
			if($this->moduloCerrado($ordenModulos[$indiceModulo - 1]->id)) {
				//Si el módulo inmediatamente anterior se encuentra cerrado,
				//se puede proceder
				return true;
			}
			else {
				//Si el módulo inmediatamente anterior no se encuentra cerrado
				//no se puede proceder
				return false;
			}
		}

		//Si no hay módulo anterior en el periodo actual
		//Se procede a validar el periodo anterior
		$periodoAnterior = $this->obtenerPeriodoAnterior();
		if(!$periodoAnterior) {
			//No existe periodo anterior
			//por lo que es el primer periodo
			return true;
		}
		else {
			return $periodoAnterior->moduloCerrado($ordenModulos[$ordenModulos->count() - 1]->id);
		}

	}

	/**
	 * Retorna el procentaje de completitud de cierres del proceso
	 * @return double
	 */
	public function porcentajeProgreso() {
		$modulos = $this->entidad->getModulos();
		$cantidadCerrados = 0;
		foreach ($modulos as $modulo) {
			if($this->moduloCerrado($modulo->id)) {
				$cantidadCerrados++;
			}
		}
		if(!$modulos->count())return 0;
		if(!$cantidadCerrados)return 0;
		return (100 / $modulos->count()) * $cantidadCerrados;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function controlCierresModulos() {
		return $this->hasMany(ControlCierreModulo::class, 'control_periodo_cierre_id', 'id');
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
