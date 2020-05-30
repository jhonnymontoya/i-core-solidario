<?php

namespace App\Models\Contabilidad;

use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\General\Dependencia;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Presupuesto\CentroCosto;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleMovimiento extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.detalle_movimientos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'movimiento_id',
		'tercero_id',
		'cuif_id',
		'dependencia_id',
		'centro_costo_id',
		'debito',
		'credito',
		'serie',
		'fecha_movimiento',
		'referencia',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_movimiento',
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
	
	public function getTieneDebitoAttribute() {
		return $this->attributes['debito'] == 0 ? false : true;
	}

	public function getTieneCreditoAttribute() {
		return $this->attributes['credito'] == 0 ? false : true;
	}

	public function getValorAttribute() {
		return $this->attributes['debito'] + $this->attributes['credito'];
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaMovimientoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_movimiento'] = null;
		}
	}

	public function setReferenciaAttribute($value) {
		$this->attributes['referencia'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
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
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	public function dependencia() {
		return $this->belongsTo(Dependencia::class, 'dependencia_id', 'id');
	}

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function cuenta() {
		return $this->belongsTo(Cuif::class, 'cuif_id', 'id');
	}

	public function movimiento() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}

	public function centroCosto() {
		return $this->belongsTo(CentroCosto::class, 'centro_costo_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
