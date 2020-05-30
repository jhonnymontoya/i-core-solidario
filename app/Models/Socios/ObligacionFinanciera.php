<?php

namespace App\Models\Socios;

use App\Models\Tesoreria\Banco;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObligacionFinanciera extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.obligaciones_financieras";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'monto',
		'tasa_mes_vencido',
		'plazo',
		'fecha_inicial',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_inicial',
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

	public function setFechaInicialAttribute($value)
	{
		if(!empty($value)) {
			$this->attributes['fecha_inicial'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_inicial'] = null;
		}
	}
	
	/**
	 * Scopes
	 */
	
	/**
	 * Filtra obligaciones financieras por el id del socio
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

	public function banco() {
		return $this->belongsTo(Banco::class, 'banco_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
