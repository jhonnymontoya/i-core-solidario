<?php

namespace App\Models\Creditos;

use App\Models\Contabilidad\Cuif;
use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParametroContable extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.parametros_contables";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tipo_cartera', //CONSUMO, VIVIENDA, COMERCIAL, MICROCREDITO
		'tipo_garantia', //GARANTIA ADMISIBLE (REAL) CON LIBRANZA, OTRAS GARANTIAS (PERSONAL) CON LIBRANZA, GARANTIA ADMISIBLE (REAL) SIN LIBRANZA, OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA
		'categoria_clasificacion', //A, B, C, D, E
		'cuif_capital_id',
		'cuif_intereses_ingreso_id',
		'cuif_intereses_por_cobrar_id',
		'cuif_intereses_anticipados_id',
		'cuif_deterioro_capital_id',
		'cuif_deterioro_intereses_id',
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

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeTipoCartera($query, $value) {
		if(!empty($value)) {
			$query->whereTipoCartera($value);
		}
	}

	public function scopeTipoGarantia($query, $value) {
		if(!empty($value)) {
			$query->whereTipoGarantia($value);
		}
	}

	public function scopeCategoriaClasificacion($query, $value) {
		if(!empty($value)) {
			$query->whereCategoriaClasificacion($value);
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

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function cuentaCapital() {
		return $this->belongsTo(Cuif::class, 'cuif_capital_id', 'id');
	}

	public function cuentaInteresesIngreso() {
		return $this->belongsTo(Cuif::class, 'cuif_intereses_ingreso_id', 'id');
	}

	public function cuentaInteresesPorCobrar() {
		return $this->belongsTo(Cuif::class, 'cuif_intereses_por_cobrar_id', 'id');
	}

	public function cuentaInteresesAnticipados() {
		return $this->belongsTo(Cuif::class, 'cuif_intereses_anticipados_id', 'id');
	}

	public function cuentaDeterioroCapital() {
		return $this->belongsTo(Cuif::class, 'cuif_deterioro_capital_id', 'id');
	}

	public function cuentaDeterioroIntereses() {
		return $this->belongsTo(Cuif::class, 'cuif_deterioro_intereses_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
