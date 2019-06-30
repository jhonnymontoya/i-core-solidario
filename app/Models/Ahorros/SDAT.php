<?php

namespace App\Models\Ahorros;

use App\Models\General\Entidad;
use App\Models\Socios\Socio;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class SDAT extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.sdats";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tipo_sdat_id',
		'socio_id',
		'valor',
		'fecha_constitucion',
		'fecha_vencimiento',
		'plazo',
		'tasa',
		'intereses_estimados',
		'retefuente_estimada',
		'estado', //SOLICITUD, CONSTITUIDO, RENOVADO, PRORROGADO, SALDADO, ANULADO
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_constitucion',
		'fecha_vencimiento',
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
		'valor'					=> 'float',
		'plazo'					=> 'integer',
		'tasa'					=> 'float',
		'intereses_estimados'	=> 'float',
		'retefuente_estimada'	=> 'float',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaConstitucionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_constitucion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_constitucion'] = null;
		}
	}

	public function setFechaVencimientoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_vencimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_vencimiento'] = null;
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

	public function movimientosSdat() {
		return $this->hasMany(MovimientoSDAT::class, 'sdat_id', 'id');
	}

	public function rendimientosSdat() {
		return $this->hasMany(RendimientoSDAT::class, 'sdat_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function tipoSdat() {
		return $this->belongsTo(TipoSdat::class, 'tipo_sdat_id', 'id');
	}

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
