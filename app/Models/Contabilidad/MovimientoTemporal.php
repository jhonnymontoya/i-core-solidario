<?php

namespace App\Models\Contabilidad;

use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class MovimientoTemporal extends Model
{
	use ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.movimientos_temporal";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'fecha_movimiento',
		'tipo_comprobante_id',
		'descripcion',
		'origen' //MANUAL, PROCESO
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

	public function getDebitosAttribute() {
		$debitos = 0;
		$debitos = DetalleMovimientoTemporal::whereEntidadId($this->attributes['entidad_id'])
						->whereMovimientoId($this->attributes['id'])
						->sum('debito');
		return $debitos;
	}

	public function getCreditosAttribute() {
		$creditos = 0;
		$creditos = DetalleMovimientoTemporal::whereEntidadId($this->attributes['entidad_id'])
						->whereMovimientoId($this->attributes['id'])
						->sum('credito');
		return $creditos;
	}

	public function getDebitosOrdenAttribute() {
		$debitosOrden = 0;
		$debitosOrden = DB::table('contabilidad.detalle_movimientos')
			->join('contabilidad.cuifs', 'contabilidad.detalle_movimientos.cuif_id', '=', 'contabilidad.cuifs.id')
			->where('contabilidad.detalle_movimientos.entidad_id', $this->attributes['entidad_id'])
			->where('contabilidad.detalle_movimientos.movimiento_id', $this->attributes['id'])
			->whereNotNull('contabilidad.cuifs.cuenta_orden')
			->sum('contabilidad.detalle_movimientos.debito');
		return $debitosOrden;
	}

	public function getCreditosOrdenAttribute() {
		$creditosOrden = 0;
		$creditosOrden = DB::table('contabilidad.detalle_movimientos_temporal')
			->join('contabilidad.cuifs', 'contabilidad.detalle_movimientos_temporal.cuif_id', '=', 'contabilidad.cuifs.id')
			->where('contabilidad.detalle_movimientos_temporal.entidad_id', $this->attributes['entidad_id'])
			->where('contabilidad.detalle_movimientos_temporal.movimiento_id', $this->attributes['id'])
			->whereNotNull('contabilidad.cuifs.cuenta_orden')
			->sum('contabilidad.detalle_movimientos_temporal.credito');
		return $creditosOrden;
	}

	public function getEstadoAttribute() {
		$estado = 'SIN CONTABILIZAR';
		return $estado;
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

	public function setDescripcionAttribute($value) {
		$this->attributes['descripcion'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	/**
	 * Scopes
	 */
	
	public function scopeComprobante($query, $value) {
		if(!empty($value)) {
			$query->where('tipo_comprobante_id', $value);
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->where('entidad_id', $value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where("descripcion", "like", "%$value%");
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

	public function detalleMovimientos() {
		return $this->hasMany(DetalleMovimientoTemporal::class, 'movimiento_id', 'id');
	}

	/**
     * Relacion uno a muchos para los movimientos de impuestos temporales
     * @return
     */
    public function movimientosImpuestosTemporales() {
        return $this->hasMany(
            MovimientoImpuestoTemporal::class, 'movimiento_termporal_id', 'id'
        );
    }
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function tipoComprobante() {
		return $this->belongsTo(TipoComprobante::class, 'tipo_comprobante_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
