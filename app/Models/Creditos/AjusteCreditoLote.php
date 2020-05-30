<?php

namespace App\Models\Creditos;

use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\ControlCierreModulo;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AjusteCreditoLote extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.ajustes_creditos_lote";

	protected $detalleError = "";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'contrapartida_cuif_id',
		'contrapartida_tercero_id',
		'fecha_proceso',
		'descripcion',
		'referencia',
		'consecutivo_proceso',
		'estado', //PRECARGA, CARGADO, EJECUTADO, ANULADO
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_proceso',
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

	public function getDetalleErrorAttribute() {
		return $this->detalleError;
	}

	public function getCantidadAjustesCreditosAttribute() {
		return $this->detallesAjusteCreditoLote->count();
	}

	public function getTotalValorAjusteAttribute() {
		$ajustesCreditos = $this->detallesAjusteCreditoLote;
		$suma = 0;
		if($ajustesCreditos->count()) {
			foreach($ajustesCreditos as $ajuste) {
				$suma += $ajuste->getValorTotal();
			}
			return $suma;
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setDescripcionAttribute($value) {
		$this->attributes['descripcion'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setFechaProcesoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_proceso'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_proceso'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}
	
	/**
	 * Funciones
	 */

	public function esValido() {
		//Se limpia el detalle de error
		$this->detalleError = "";

		//Se valida que el estado del proceso sea cargado
		if($this->estado != 'CARGADO') {
			$this->detalleError = "Estado del proceso no es válido";
			return false;
		}

		//Se valida que el módulo de contabilidad no se encuentre cerrado para la fecha de proceso
		$cierre = ControlCierreModulo::entidadId($this->entidad_id)->whereModuloId(2)->orderBy('fecha_cierre', 'desc')->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($this->fecha_proceso)) {
				$this->detalleError = "El módulo de contabilidad se encuentra cerrado para la fecha de proceso";
				return false;
			}
		}

		//Se valida que el módulo de créditos no se encuentre cerrado para la fecha de proceso
		$cierre = ControlCierreModulo::entidadId($this->entidad_id)->whereModuloId(7)->orderBy('fecha_cierre', 'desc')->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($this->fecha_proceso)) {
				$this->detalleError = "El módulo de créditos se encuentra cerrado para la fecha de proceso";
				return false;
			}
		}

		//Se valida que exista el tipo de comprobante para proceso AJCR
		$tipoComprobante = TipoComprobante::entidadId($this->entidad_id)->whereCodigo('AJCR')->uso('PROCESO')->first();
		if($tipoComprobante == null) {
			$this->detalleError = "No existe tipo de comprobante para contabilización de proceso";
			return false;
		}

		//Se valida que los ajustes no queden con saldos negativos
		$detalleAjustesAhorros = $this->detallesAjusteCreditoLote;
		foreach($detalleAjustesAhorros as $ajuste) {
			if($ajuste->getSaldoObligacion('31/12/2200') + $ajuste->getValorCapital() < 0) {
				$mensaje = "El saldo para la obligación %s queda con saldo negativo";
				$this->detalleError = sprintf($mensaje, $ajuste->getSolicitudCredito()->numero_obligacion);
				return false;
			}
		}
		return true;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function detallesAjusteCreditoLote() {
		return $this->hasMany(DetalleAjusteCreditoLote::class, 'ajuste_credito_lote_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function cuif() {
		return $this->belongsTo(Cuif::class, 'contrapartida_cuif_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'contrapartida_tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
