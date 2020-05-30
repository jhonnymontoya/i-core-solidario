<?php

namespace App\Models\Creditos;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentacionModalidad extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.documentacion_modalidades";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'modalidad_id',
		'documento',
		'obligatorio',
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
		'obligatorio'		=> 'boolean'
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setDocumentoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['documento'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
		}
		else {
			$this->attributes['documento'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeModalidadId($query, $value) {
		if(!empty($value)) {
			$query->whereModalidadId($value);
		}
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('documento', 'like', '%' . $value . '%');
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

	public function modalidad() {
		return $this->belongsTo(Modalidad::class, 'modalidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function solicitudesCreditos() {
		return $this->belongsToMany(SolicitudCredito::class, 'creditos.documento_solicitud', 'documento_modalidad_id', 'solicitud_credito_id')
					->withPivot('cumple')
					->withTimestamps();
	}
}
