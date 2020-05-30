<?php

namespace App\Models\Socios;

use App\Models\Contabilidad\Movimiento;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocioRetiro extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.socios_retiros";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'socio_id',
		'causa_retiro_id',
		'movimiento_id',
		'fecha_solicitud_retiro',
		'fecha_liquidacion',
		'observacion',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_solicitud_retiro',
		'fecha_liquidacion',
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

	public function setFechaSolicitudRetiroAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_solicitud_retiro'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_solicitud_retiro'] = null;
		}
	}

	public function setFechaLiquidacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_liquidacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_liquidacion'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->whereHas('socio', function($q) use($value){
			return $q->entidad($value);
		});
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

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}

	public function causaRetiro() {
		return $this->belongsTo(CausaRetiro::class, 'causa_retiro_id', 'id');
	}

	public function movimiento() {
		return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
