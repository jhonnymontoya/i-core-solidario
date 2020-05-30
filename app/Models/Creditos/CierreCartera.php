<?php

namespace App\Models\Creditos;

use App\Models\Creditos\Modalidad;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\ControlCierreModulo;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Recaudos\Pagaduria;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CierreCartera extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.cierres_cartera";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'control_cierre_modulo_id',
		'tercero_id',
		'tercero_numero_identificacion',
		'tercero_nombre',
		'socio_estado',
		'pagaduria_id',
		'solicitud_credito_id',
		'numero_obligacion',
		'fecha_desembolso',
		'valor_credito',
		'tasa',
		'plazo',
		'valor_cuota',
		'altura_cuota',
		'numero_cuotas_pendientes',
		'modalidad_id',
		'modalidad_nombre',
		'saldo_capital',
		'saldo_intereses',
		'saldo_seguro',
		'dias_vencidos',
		'capital_vencido',
		'tipo_cartera',
		'tipo_garantia',
		'forma_pago',
		'periodicidad',
		'fecha_descuento_capital',
		'fecha_descuento_intereses',
		'fecha_terminacion_programada',
		'fecha_ultimo_pago',
		'calificacion_periodo_anterior',
		'calificacion_actual',
		'calificacion_final',
		'porcentaje_deterioro_capital',
		'valor_aporte_deterioro',
		'base_deterioro',
		'deterioro_capital',
		'porcentaje_deterioro_intereses',
		'deterioro_intereses',
		'fecha_cancelacion',
		'estado_obligacion',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_desembolso',
		'fecha_descuento_capital',
		'fecha_descuento_intereses',
		'fecha_terminacion_programada',
		'fecha_ultimo_pago',
		'fecha_cancelacion',
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
		'valor_credito'						=> 'float',
		'tasa'								=> 'float',
		'plazo'								=> 'integer',
		'valor_cuota'						=> 'float',
		'altura_cuota'						=> 'integer',
		'numero_cuotas_pendientes'			=> 'integer',
		'dias_vencidos'						=> 'integer',
		'capital_vencido'					=> 'float',
		'porcentaje_deterioro_capital'		=> 'float',
		'valor_aporte_deterioro'			=> 'float',
		'base_deterioro'					=> 'float',
		'deterioro_capital'					=> 'float',
		'porcentaje_deterioro_intereses'	=> 'float',
		'deterioro_intereses'				=> 'float'
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaDesembolsoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_desembolso'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_desembolso'] = null;
		}
	}

	public function setFechaDescuentoCapitalAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_descuento_capital'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_descuento_capital'] = null;
		}
	}

	public function setFechaDescuentoInteresesAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_descuento_intereses'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_descuento_intereses'] = null;
		}
	}

	public function setFechaTerminacionProgramadaAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_terminacion_programada'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_terminacion_programada'] = null;
		}
	}

	public function setFechaUltimoPagoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_ultimo_pago'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_ultimo_pago'] = null;
		}
	}

	public function setFechaCancelacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_cancelacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_cancelacion'] = null;
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

	public function controlCierreModulo() {
		return $this->belongsTo(ControlCierreModulo::class, 'control_cierre_modulo_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function pagaduria() {
		return $this->belongsTo(Pagaduria::class, 'pagaduria_id', 'id');
	}

	public function solicitudCredito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}

	public function modalidad() {
		return $this->belongsTo(Modalidad::class, 'modalidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
