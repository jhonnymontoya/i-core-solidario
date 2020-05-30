<?php

namespace App\Models\Socios;

use App\Models\General\Tercero;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiario extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.beneficiarios";

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
	
	/**
	 * Scopes
	 */
	
	/**
	 * Filtra beneficiarios por socio id
	 * @param  [type] $value id del socio
	 * @return [type]        [description]
	 */
	public function scopeSocioid($query, $value) {
		if(!empty($value)) {
			$query->whereSocioId($value);
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

	public function parentesco() {
		return $this->belongsTo(Parentesco::class, 'parentesco_id', 'id');
	}

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
