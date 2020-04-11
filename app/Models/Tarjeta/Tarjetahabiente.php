<?php

namespace App\Models\Tarjeta;

use App\Models\Ahorros\CuentaAhorro;
use App\Models\Contabilidad\Cuif;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Tarjeta\Producto;
use App\Models\Tarjeta\Tarjeta;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarjetahabiente extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "tarjeta.tarjetahaientes";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'producto_id',
		'tarjeta_id',
		'tercero_id',
		'cuenta_ahorro_id',
		'solicitud_credito_id',
		'cupo',
		'fecha_asignacion',
		'estado', //ASIGNADA, ACTIVA, INACTIVA, BLOQUEADA, CANCELADA
		'fecha_cancelacion',
		//'numero_cuenta_corriente', --no es fillable
		//'numero_cuenta_vista', --no es fillable
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_asignacion',
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
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaAsignacionAttribute($value) {
		if(!empty($value)) {
			$fecha = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
			$this->attributes['fecha_asignacion'] = $fecha;
		}
		else {
			$this->attributes['fecha_asignacion'] = null;
		}
	}

	public function setFechaCancelacionAttribute($value) {
		if(!empty($value)) {
			$fecha = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
			$this->attributes['fecha_cancelacion'] = $fecha;
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
	 * Asigna número de cuenta de ahorros basado en la tarjeta asociada
	 * @return type|boolean
	 */
	public function asignarNumeroCuentaCorriente() {
		$tarjeta = $this->tarjeta;
		if (!$tarjeta){
			return false;
		}
		$numeroTarjeta = $tarjeta->numero;
		$numeroCuentaCorriente = substr($numeroTarjeta, 6);
		$this->numero_cuenta_corriente = $numeroCuentaCorriente;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relaciones Muchos a uno
	 */

	/**
	 * Relación muchos a uno para la entidad a la que pertenece
	 * @return Entidad
	 */
	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function producto() {
		return $this->belongsTo(Producto::class, 'producto_id', 'id');
	}

	public function tarjeta() {
		return $this->belongsTo(Tarjeta::class, 'tarjeta_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function egresoComision() {
		return $this->belongsTo(Cuif::class, 'egreso_comision_cuif_id', 'id');
	}

	public function cuotaManejoCuenta() {
		return $this->belongsTo(Cuif::class, 'cuota_manejo_cuif_id', 'id');
	}

	public function cuentaAhorro() {
		return $this->belongsTo(CuentaAhorro::class, 'cuenta_ahorro_id', 'id');
	}

	public function solicitudCredito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
