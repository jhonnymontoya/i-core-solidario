<?php

namespace App\Models\Ahorros;

use App\Models\Contabilidad\Cuif;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;

class TipoSDAT extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.tipos_sdat";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'codigo',
		'nombre',
		'capital_cuif_id',
		'intereses_cuif_id',
		'intereses_por_pagar_cuif_id',
		'apalancamiento_cupo', //Este campo se alimenta por /cupoCredito
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
		'esta_activo'			=> 'boolean',
		'apalancamiento_cupo'	=> 'float'
	];

	/**
	 * Getters personalizados
	 */

	public function getNombreCompletoAttribute() {
		$nombre = "%s-%s";
		$nombre = sprintf($nombre, $this->codigo, $this->nombre);
		return $nombre;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeActivo($query, $value = true) {
		$value = empty($value) == true ? true : $value;
		$query->whereEstaActivo($value);
	}
	
	/**
	 * Funciones
	 */

	public function obtenerCondicionPlazoMonto($plazo, $monto) {
		/*
			1 = Plazo no configurado
			2 = Monto no configurado
		*/
		$condiciones = $this->condicionesSDAT()
			->whereRaw("? between plazo_minimo and plazo_maximo", [$plazo])
			->get();
		if($condiciones->count() == 0) {
			throw new Exception("No se encuentra parametrizado el plazo", 1);
		}
		$condicion = null;
		foreach ($condiciones as $cond) {
			if($cond->monto_minimo <= $monto && $cond->monto_maximo >= $monto) {
				$condicion = $cond;
				break;
			}
		}
		if($condicion == null) {
			throw new Exception("No se encuentra parametrizado el monto", 2);
		}
		return $condicion;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function condicionesSDAT() {
		return $this->hasMany(CondicionSDAT::class, 'tipo_sdat_id', 'id');
	}

	public function SDATs() {
		return $this->hasMany(SDAT::class, 'tipo_sdat_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function capitalCuif() {
		return $this->belongsTo(Cuif::class, 'capital_cuif_id', 'id');
	}

	public function interesesCuif() {
		return $this->belongsTo(Cuif::class, 'intereses_cuif_id', 'id');
	}

	public function interesesPorPagarCuif() {
		return $this->belongsTo(Cuif::class, 'intereses_por_pagar_cuif_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
