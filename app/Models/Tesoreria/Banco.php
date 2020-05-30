<?php

namespace App\Models\Tesoreria;

use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Socios\ObligacionFinanciera;
use App\Models\Socios\TarjetaCredito;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "tesoreria.bancos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'codigo',
		'nombre',
		'sap',
		'esta_activo',
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
		'esta_activo' => 'boolean',
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

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("codigo", "like", "%$value%")
						->orWhere("nombre", "like", "%$value%");
		}
	}

	public function scopeActivo($query, $value = true) {
		return $query->where('esta_activo', $value);
	}

	/**Codigo de transferencias por archivo de disperción*/
	public function scopeSap($query, $value) {
		if(!empty($value)) {
			return $query->where('codigo', $value);
		}
	}

	public function scopeEntidadBanco($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->whereHas('entidad', function($q) use($value){
			$q->where('id', $value);
		});
	}

	public function scopeId($query, $value) {
		if(!empty($value)) {
			return $query->whereId($value);
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

	public function tarjetasCredito() {
		return $this->hasMany(TarjetaCredito::class, 'banco_id', 'id');
	}

	public function obligacionesFinancieras() {
		return $this->hasMany(ObligacionFinanciera::class, 'banco_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function tercero() {
		return $this->belongsToMany(Tercero::class, 'tesoreria.cuentas_bancarias', 'banco_id', 'tercero_id')
					->withPivot('tipo_cuenta')
					->withPivot('numero')
					->withTimestamps();
	}
}
