<?php

namespace App\Models\Ahorros;

use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\ControlCierreModulo;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AjusteAhorroLote extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.ajustes_ahorros_lote";

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

	public function getCantidadAjustesAhorrosAttribute() {
		return $this->detallesAjusteAhorroLote->count();
	}

	public function getTotalValorAjusteAttribute() {
		$ajustesCreditos = $this->detallesAjusteAhorroLote;
		$suma = 0;
		if($ajustesCreditos->count()) {
			foreach($ajustesCreditos as $ajuste) {
				$valores = json_decode($ajuste->detalle);
				if(!empty($valores->valor))$suma += $valores->valor;
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

		//Se valida que el módulo de ahorros no se encuentre cerrado para la fecha de proceso
		$cierre = ControlCierreModulo::entidadId($this->entidad_id)->whereModuloId(6)->orderBy('fecha_cierre', 'desc')->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($this->fecha_proceso)) {
				$this->detalleError = "El módulo de ahorros se encuentra cerrado para la fecha de proceso";
				return false;
			}
		}

		//Se valida que exista el tipo de comprobante para proceso AJAH
		$tipoComprobante = TipoComprobante::entidadId($this->entidad_id)->whereCodigo('AJAH')->uso('PROCESO')->first();
		if($tipoComprobante == null) {
			$this->detalleError = "No existe tipo de comprobante para contabilización de proceso";
			return false;
		}

		//Se valida que los ajustes no queden con saldos negativos
		$detalleAjustesAhorros = $this->detallesAjusteAhorroLote;
		foreach($detalleAjustesAhorros as $ajuste) {
			if($ajuste->getSaldoModalidadAhorro('31/12/2200') + $ajuste->getValor() < 0) {
				$mensaje = "El saldo para la modalidad %s del socio %s queda con saldo negativo";
				$this->detalleError = sprintf($mensaje, $ajuste->getModalidadAhorro()->codigo, $ajuste->getSocio()->tercero->nombre_corto);
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

	public function detallesAjusteAhorroLote() {
		return $this->hasMany(DetalleAjusteAhorroLote::class, 'ajuste_ahorro_lote_id', 'id');
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
