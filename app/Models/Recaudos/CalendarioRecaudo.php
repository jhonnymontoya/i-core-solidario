<?php

namespace App\Models\Recaudos;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalendarioRecaudo extends Model
{
	use ICoreTrait, ICoreModelTrait;
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.calendario_recaudos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'pagaduria_id',
		'numero_periodo',
		'fecha_reporte',
		'fecha_recaudo',
		'estado', /*PROGRAMADO, EJECUTADO*/
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_reporte',
		'fecha_recaudo',
		'created_at',
		'updated_at',
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

	public function setFechaReporte($value) {
		if(!empty($value)) {
			$this->attributes['fecha_reporte'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_reporte'] = null;
		}
	}

	public function setFechaRecaudo($value) {
		if(!empty($value)) {
			$this->attributes['fecha_recaudo'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_recaudo'] = null;
		}
	}
		
	/**
	 * Scopes
	 */

	public function scopeEstado($query, $value) {
		if(!empty($value)) {
			$query->whereEstado($value);
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

	public function controlProceso() {
		return $this->hasMany(ControlProceso::class, 'calendario_recaudo_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function pagaduria() {
		return $this->belongsTo(Pagaduria::class, 'pagaduria_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
