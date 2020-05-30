<?php

namespace App\Models\Tarjeta;

use App\Models\Contabilidad\Cuif;
use App\Models\Creditos\Modalidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "tarjeta.productos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'codigo',
		'nombre',
		'credito', //Dice si es de modalidad de crédito (Cambo booleano)
		'ahorro', //Dice si es de modalidad de ahorro (Cambo booleano)
		'vista', //Dice si es de modalidad de vista (Cambo booleano)
		'convenio',
		'tipo_pago_cuota_manejo', //ANTICIPADO, VENCIDO
		'valor_cuota_manejo_mes',
		'periodicidad_cuota_manejo', //ANUAL, SEMESTRAL, CUATRIMESTRAL, TRIMESTRAL, BIMESTRAL, MENSUAL, QUINCENAL, CATORCENAL, DECADAL, SEMANAL, DIARIO
		'meses_sin_cuota_manejo',
		'modalidad_credito_id',
		'cuenta_compensacion_cuif_id',
		'ingreso_comision_cuif_id',
		'egreso_comision_cuif_id',
		'cuota_manejo_cuif_id',
		'numero_retiros_sin_cobro_red',
		'numero_retiros_sin_cobro_otra_red',
		'esta_activo'
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
		'entidad_id' => 'integer',
		'credito' => 'boolean',
		'ahorro' => 'boolean',
		'vista' => 'boolean',
		'esta_activo' => 'boolean'
	];

	/**
	 * Getters personalizados
	 */

	public function getConvenioEntidadAttribute() {
		return str_pad($this->attributes['convenio'], 8, "0", STR_PAD_LEFT);
	}

	public function getNombreCompletoAttribute() {
		return $this->codigo . " - " . $this->nombre;
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

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where("codigo", "like", "%$value%")->orWhere("nombre", "like", "%$value%");
		}
	}

	public function scopeConvenio($query, $value) {
		if(!empty($value)) {
			$query->whereConvenio($value);
		}
	}

	/**
	 * Scope para seleccionar productos tarjeta afinidad activos o no
	 * @param type $query 
	 * @param type|bool $value 
	 * @return type
	 */
	public function scopeActivo($query, $value = true) {
		return $query->where('esta_activo', $value);
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

	public function tarjetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'producto_id', 'id');
	}

	public function logTransaccionesRecibidas() {
		return $this->hasMany(LogMovimientoTransaccionRecibido::class, 'producto_id', 'id');
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

	public function modalidadCredito() {
		return $this->belongsTo(Modalidad::class, 'modalidad_credito_id', 'id');
	}

	public function cuentaCompensacion() {
		return $this->belongsTo(Cuif::class, 'cuenta_compensacion_cuif_id', 'id');
	}

	public function ingresoComision() {
		return $this->belongsTo(Cuif::class, 'ingreso_comision_cuif_id', 'id');
	}

	public function egresoComision() {
		return $this->belongsTo(Cuif::class, 'egreso_comision_cuif_id', 'id');
	}

	public function cuotaManejoCuenta() {
		return $this->belongsTo(Cuif::class, 'cuota_manejo_cuif_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
