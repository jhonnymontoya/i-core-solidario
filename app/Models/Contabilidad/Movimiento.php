<?php

namespace App\Models\Contabilidad;

use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\General\Entidad;
use App\Models\Recaudos\RecaudoCaja;
use App\Models\Socios\SocioRetiro;
use App\Models\Tarjeta\LogMovimientoTransaccionRecibido;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.movimientos";

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
		'origen',
		//'anulado_por_usuario',
		//'anulado_por_nombre'
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
		$debitos = $this->detalleMovimientos->sum('debito');
		return $debitos;
	}

	public function getCreditosAttribute() {
		$creditos = 0;
		$creditos = $this->detalleMovimientos->sum('credito');
		return $creditos;
	}

	public function getDebitosOrdenAttribute() {
		$debitosOrden = 0;
		$debitosOrden = $this->detalleMovimientos()->whereHas('cuenta', function($query){
			$query->whereNotNull('cuenta_orden');
		})->sum('debito');
		return $debitosOrden;
	}

	public function getCreditosOrdenAttribute() {
		$creditosOrden = 0;
		$creditosOrden = $this->detalleMovimientos()->whereHas('cuenta', function($query){
			$query->whereNotNull('cuenta_orden');
		})->sum('credito');
		return $creditosOrden;
	}

	public function getEstadoAttribute() {
		if (empty($this->attributes["causa_anulado_id"])) {
			$estado = 'CONTABILIZADO';
		}
		else {
			$estado = 'ANULADO';
		}
		return $estado;
	}
	
	/**
	 * Setters Personalizados
	 */
	
	public function setFechaMovimientoAttribute($value) {
		if(!empty($value)) {
			$data = explode('/', $value);
			$this->attributes['anio'] = $data[2];
			$this->attributes['mes'] = $data[1];
			$this->attributes['dia'] = $data[0];
			$this->attributes['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['anio'] = null;
			$this->attributes['mes'] = null;
			$this->attributes['dia'] = null;
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

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where("numero_comprobante", "like", "%$value%")
					->orWhere("descripcion", "like", "%$value%");
		}
	}

	public function scopeOrigen($query, $value) {
		if(!empty($value)) {
			$query->where("origen", $value);
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->where('entidad_id', $value);
	}

	public function scopeAnulado($query, $value = true) {
		if ($value) {
			$query->whereNotNull('causa_anulado_id');
		}
		else {
			$query->whereNull('causa_anulado_id');
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
		return $this->hasMany(DetalleMovimiento::class, 'movimiento_id', 'id');
	}

	public function movimientosAhorros() {
		return $this->hasMany(MovimientoAhorro::class, 'movimiento_id', 'id');
	}

	public function controlesInteresesCartera() {
		return $this->hasMany(ControlInteresCartera::class, 'movimiento_id', 'id');
	}

	public function controlesSegurosCartera() {
		return $this->hasMany(ControlSeguroCartera::class, 'movimiento_id', 'id');
	}

	public function retirosSocios() {
		return $this->hasMany(SocioRetiro::class, 'movimiento_id', 'id');
	}

	public function recaudosCaja() {
		return $this->hasMany(RecaudoCaja::class, 'movimiento_id', 'id');
	}

	public function deterioros() {
		return $this->hasMany(Deterioro::class, 'movimiento_id', 'id');
	}

	public function logsMovimientosTransaccionesRecibidas() {
		return $this->hasMany(
			LogMovimientoTransaccionRecibido::class,
			'movimiento_id',
			'id'
		);
	}

	/**
     * Relacion uno a muchos para los movimientos de impuestos
     * @return
     */
    public function movimientosImpuestos() {
        return $this->hasMany(MovimientoImpuesto::class, 'movimiento_id', 'id');
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

	public function causaAnulacionMovimiento() {
		return $this->belongsTo(CausaAnulacionMovimiento::class, 'causa_anulado_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
