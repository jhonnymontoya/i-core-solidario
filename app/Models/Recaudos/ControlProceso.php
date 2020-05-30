<?php

namespace App\Models\Recaudos;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlProceso extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.control_procesos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'calendario_recaudo_id',
		'pagaduria_id',
		'fecha_generacion',
		'fecha_aplicacion',
		'fecha_ajuste',
		'estado', //GENERADO, ANULADO, APLICADO, AJUSTADO
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_generacion',
		'fecha_aplicacion',
		'fecha_ajuste',
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

	public function getTotalAplicarAttribute() {
		if(!$this->datosParaAplicar->count())return 0;
		$totalParaAplicar = $this->datosParaAplicar()->select(DB::raw('SUM(valor_descontado) as totalParaAplicar'))->first();
		return $totalParaAplicar->totalParaAplicar;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setFechaGeneracionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_generacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_generacion'] = null;
		}
	}

	public function setFechaAplicacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_aplicacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_aplicacion'] = null;
		}
	}

	public function setFechaAjusteAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_ajuste'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_ajuste'] = null;
		}
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

	public function recaudosNomina() {
		return $this->hasMany(RecaudoNomina::class, 'control_proceso_id', 'id');
	}

	public function datosParaAplicar() {
		return $this->hasMany(DatoParaAplicar::class, 'control_proceso_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function calendarioRecaudo() {
		return $this->belongsTo(CalendarioRecaudo::class, 'calendario_recaudo_id', 'id');
	}

	public function pagaduria() {
		return $this->belongsTo(Pagaduria::class, 'pagaduria_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
