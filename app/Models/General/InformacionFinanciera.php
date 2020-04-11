<?php

namespace App\Models\General;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformacionFinanciera extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.informaciones_financieras";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_corte',
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

	public function getActivosAttribute() {
		if(empty($this->attributes['activos']) || $this->attributes['activos'] == null) {
			return 0;
		}
		else {
			return $this->attributes['activos'];
		}
	}

	public function getPasivosAttribute() {
		if(empty($this->attributes['pasivos']) || $this->attributes['pasivos'] == null) {
			return 0;
		}
		else {
			return $this->attributes['pasivos'];
		}
	}

	public function getIngresoMensualAttribute() {
		if(empty($this->attributes['ingreso_mensual']) || $this->attributes['ingreso_mensual'] == null) {
			return 0;
		}
		else {
			return $this->attributes['ingreso_mensual'];
		}
	}

	public function getGastoMensualAttribute() {
		if(empty($this->attributes['gasto_mensual']) || $this->attributes['gasto_mensual'] == null) {
			return 0;
		}
		else {
			return $this->attributes['gasto_mensual'];
		}
	}

	public function getPatrimonioAttribute() {
		if(empty($this->attributes['patrimonio']) || $this->attributes['patrimonio'] == null) {
			return 0;
		}
		else {
			return $this->attributes['patrimonio'];
		}
	}

	public function getResultadoAttribute() {
		if(empty($this->attributes['resultado']) || $this->attributes['resultado'] == null) {
			return 0;
		}
		else {
			return $this->attributes['resultado'];
		}
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setActivosAttribute($value) {
		if(!empty($value)) {
			$this->attributes['activos'] = $value;
			$this->attributes['patrimonio'] = $this->activos - $this->pasivos;
		}
		else {
			$this->attributes['activos'] = 0;
			$this->attributes['patrimonio'] = 0;
		}
	}

	public function setPasivosAttribute($value) {
		if(!empty($value)) {
			$this->attributes['pasivos'] = $value;
			$this->attributes['patrimonio'] = $this->activos - $this->pasivos;
		}
		else {
			$this->attributes['pasivos'] = 0;
			$this->attributes['patrimonio'] = 0;
		}
	}

	public function setIngresoMensualAttribute($value) {
		if(!empty($value)) {
			$this->attributes['ingreso_mensual'] = $value;
			$this->attributes['resultado'] = $this->ingreso_mensual - $this->gasto_mensual;
		}
		else {
			$this->attributes['ingreso_mensual'] = 0;
			$this->attributes['resultado'] = 0;
		}
	}

	public function setGastoMensualAttribute($value) {
		if(!empty($value)) {
			$this->attributes['gasto_mensual'] = $value;
			$this->attributes['resultado'] = $this->ingreso_mensual - $this->gasto_mensual;
		}
		else {
			$this->attributes['gasto_mensual'] = 0;
			$this->attributes['resultado'] = 0;
		}
	}
	
	public function setFechaCorteAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_corte'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_corte'] = null;
		}
	}
	/**
	 * Scopes
	 */
	
	/**
	 * Funciones
	 */
	public function hayCampos() {
		$conCampos = false;
		$conCampos = empty($this->attributes['activos'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['pasivos'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['ingreso_mensual'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['gasto_mensual'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['fecha_corte'])?false:true;
		if($conCampos)return true;

		return false;
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
	
	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}
