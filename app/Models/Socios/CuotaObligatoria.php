<?php

namespace App\Models\Socios;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\General\Entidad;
use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuotaObligatoria extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.cuotas_obligatorias";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'socio_id',
		'modalidad_ahorro_id',
		'tipo_calculo',
		'valor',
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

	public function getEsReglamentariaAttribute() {
		$reglamentaria = true;
		if($this->modalidadAhorro->tipo_calculo != $this->tipo_calculo) {
			$reglamentaria = false;
		}
		if($this->modalidadAhorro->valor != $this->valor) {
			$reglamentaria = false;
		}
		return $reglamentaria;
	}
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */

	/**
	 * Scope para filtrar cuotas obligatorias por entidad
	 * @param type $query
	 * @param type $valor entidad
	 * @return type
	 */
	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	/**
	 * Scope para filtrar cuotas obligatorias por socio
	 * @param type $query
	 * @param type $valor
	 * @return type
	 */
	public function scopeModalidadAhorroId($query, $valor) {
		if(!empty($valor)) {
			$query->whereModalidadAhorroId($valor);
		}
	}

	/**
	 * Scope para filtrar cuotas obligatorias por socio
	 * @param type $query
	 * @param type $valor
	 * @return type
	 */
	public function scopeSocioId($query, $valor) {
		if(!empty($valor)) {
			$query->whereSocioId($valor);
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

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}

	public function modalidadAhorro() {
		return $this->belongsTo(ModalidadAhorro::class, 'modalidad_ahorro_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
