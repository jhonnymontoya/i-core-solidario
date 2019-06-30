<?php

namespace App\Models\Ahorros;

use App\Models\Socios\Socio;
use App\Models\Tarjeta\Tarjetahabiente;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class CuentaAhorro extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.cuentas_ahorros";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tipo_cuenta_ahorro_id',
		'titular_socio_id',
		'nombre_deposito',
		'numero_cuenta',
		'fecha_apertura',
		'fecha_cierre',
		'estado', //APERTURA, ACTIVA, INACTIVA, CERRADA
		'cupo_flexible',
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
		'cupo_flexible'		=> 'float'
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

	public function setNombreDepositoAttribute($value) {
		$this->attributes['nombre_deposito'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("numero_cuenta", "like", "%$value%")->orWhere('nombre_deposito', "like", "%$value%");
		}
	}

	public function scopeEstado($query, $value) {
		if(!empty($value)) {
			return $query->whereEstado($value);
		}
	}
	
	/**
	 * Funciones
	 */

	public static function obtenerSiguienteNumeroCuentaAhorros($entidad = 0) {
		$sql = "select ahorros.fn_asignacion_numero_cuenta_ahorros(?) as numero_cuenta";
		$res = DB::select($sql, [$entidad]);
		if(empty($res)) return null;
		return $res[0]->numero_cuenta;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relación uno a muchos para tarjetahabientes
	 * @return Colección Tarjetahabiente
	 */
	public function tarjetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'cuenta_ahorro_id', 'id');
	}

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

	/**
	 * Relación muchos a uno para tipo cuenta ahorros
	 * @return TipoCuentaAhorro
	 */
	public function tipoCuentaAhorro() {
		return $this->belongsTo(TipoCuentaAhorro::class, 'tipo_cuenta_ahorro_id', 'id');
	}

	/**
	 * Relación muchos a uno para el socio titular
	 * @return type
	 */
	public function socioTitular() {
		return $this->belongsTo(Socio::class, 'titular_socio_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
