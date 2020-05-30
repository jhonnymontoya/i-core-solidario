<?php

namespace App\Models\Recaudos;

use App\Models\Contabilidad\Cuif;
use App\Models\Creditos\CierreCartera;
use App\Models\General\Ciudad;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Recaudos\CalendarioRecaudo;
use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagaduria extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.pagadurias";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tercero_empresa_id',
		'ciudad_id',
		'cuenta_por_cobrar_patronal_cuif_id',
		'nombre',
		'contacto',
		'contacto_email',
		'contacto_telefono',
		'periodicidad_pago', //DIARIO, SEMANAL, DECADAL, CATORCENAL, QUINCENAL, MENSUAL, BIMESTRAL, TRIMESTRAL, CUATRIMESTRAL, SEMESTRAL, ANUAL
		'paga_prima',
		'fecha_inicio_recaudo',
		'esta_activa',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_inicio_recaudo',
		'fecha_inicio_reporte',
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
		'esta_activa'		 	=> 'boolean',
		'paga_prima'			=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setContactoAttribute($value) {
		$this->attributes['contacto'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setFechaInicioRecaudoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_inicio_recaudo'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_inicio_recaudo'] = null;
		}
	}

	public function setFechaInicioReporteAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_inicio_reporte'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_inicio_reporte'] = null;
		}
	}
		
	/**
	 * Scopes
	 */
	
	public function scopeActiva($query, $value = true) {
		return $query->where('esta_activa', $value);
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("nombre", "like", "%$value%");
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

	public function socios() {
		return $this->hasMany(Socio::class, 'pagaduria_id', 'id');
	}

	public function calendarioRecaudos() {
		return $this->hasMany(CalendarioRecaudo::class, 'pagaduria_id', 'id');
	}

	public function conceptosRecaudos() {
		return $this->hasMany(ConceptoRecaudos::class, 'pagaduria_id', 'id');
	}

	public function controlProceso() {
		return $this->hasMany(ControlProceso::class, 'pagaduria_id', 'id');
	}

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'pagaduria_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function terceroEmpresa() {
		return $this->belongsTo(Tercero::class, 'tercero_empresa_id', 'id');
	}

	public function ciudad() {
		return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id');
	}

	public function cuentaPorCobrar() {
		return $this->belongsTo(Cuif::class, 'cuenta_por_cobrar_patronal_cuif_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
