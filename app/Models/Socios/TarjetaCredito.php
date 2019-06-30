<?php

namespace App\Models\Socios;

use App\Models\Tesoreria\Banco;
use App\Models\Tesoreria\Franquicia;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarjetaCredito extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.tarjetas_credito";

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
	 * Filtra las tarjetas de crédito por socio
	 * @param  [type] $query [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function scopeSocioId($query, $value) {
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

	public function socio() {
		return $this->belongsTo(Socio::class, 'socio_id', 'id');
	}

	public function franquicia() {
		return $this->belongsTo(Franquicia::class, 'franquicia_id', 'id');
	}

	public function banco() {
		return $this->belongsTo(Banco::class, 'banco_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
