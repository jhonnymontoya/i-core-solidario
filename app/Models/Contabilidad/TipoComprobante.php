<?php

namespace App\Models\Contabilidad;

use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoComprobante extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.tipos_comprobantes";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'nombre',
		'codigo',
		'plantilla_impresion',
		'comprobante_diario',
		'tipo_consecutivo',
		'modulo_id',
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
	];


	/**
	 * Getters personalizados
	 */
	
	public function getNombreCompletoAttribute() {
		return $this->attributes['codigo'] . ' - ' . $this->attributes['nombre'];
	}

	public function getParaComprobanteAttribute() {
		$res = false;
		switch($this->attributes['modulo_id']) {
			case 1:
				$res = true;
				break;
			case 2:
				$res = true;
				break;
			case 5:
				$res = true;
				break;			
			default:
				$res = false;
				break;
		}
		return $res;
	}

	public function getEsUsoManualAttribute() {
		return $this->attributes['uso'] == 'MANUAL' ? true : false;
	}

	public function getEsUsoProcesoAttribute() {
		return $this->attributes['uso'] == 'PROCESO' ? true : false;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
		}
		else {
			$this->attributes['codigo'] = null;
		}
	}

	public function setNombreAttribute($value) {
		if(!empty($value)) {
			$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
		}
		else {
			$this->attributes['nombre'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->with('entidad')->whereHas('entidad', function($q) use($value){
			$q->whereId($value);
		});
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where("codigo", "like", "%$value%")
					->orWhere("nombre", "like", "%$value%");
		}
	}

	public function scopeParaComprobante($query) {
		$query->whereIn('modulo_id', [1, 2, 5]);
	}

	public function scopeUso($query, $value) {
		if(!empty($value)) {
			$query->whereUso($value);
		}
		else {
			$query->whereUso('MANUAL');
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

	public function movimientos() {
		return $this->hasMany(Movimiento::class, 'tipo_comprobante_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function modulo() {
		return $this->belongsTo(Modulo::class, 'modulo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
