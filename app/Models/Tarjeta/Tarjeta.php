<?php

namespace App\Models\Tarjeta;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarjeta extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "tarjeta.tarjetas";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'numero',
		'vencimiento_mes',
		'vencimiento_anio',
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

	protected $appends = [
		'numeroFormateado',
		'vencimiento'
	];

	/**
	 * Getters personalizados
	 */

	public function getNumeroFormateadoAttribute() {
		$longitud = strlen($this->numero);
		$numeroTarjeta = substr($this->numero, 0, 4);
		for($incremento = 4; $incremento < $longitud; $incremento++) {
			if($incremento % 4 == 0)$numeroTarjeta .= "-";
			$numeroTarjeta .= $this->numero[$incremento];
		}
		return $numeroTarjeta;
	}

	public function getVencimientoAttribute() {
		return $this->vencimiento_anio . '/' . $this->vencimiento_mes;
	}
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	/**
	 * Ordena las tarjetas sin el código de chequeo
	 * @param type $query 
	 * @return type
	 */
	public function scopeOrden($query) {
		return $query->orderBy(DB::raw("CONVERT(bigint, LEFT(numero, len(numero) - 1))"));
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->whereHas("tarjetahabientes", function($query) use($value){
				$query->whereHas("tercero", function($q) use($value){
					$q->search($value);
				});
			})->orWhere("numero", "like", "%$value%");
		}
	}
	
	/**
	 * Funciones
	 */

	public static function obtenerTarjetaDisponible() {
		$tarjeta = self::entidadId()->doesntHave('tarjetahabientes')->orden()->take(1)->first();
		return $tarjeta;
	}

	public static function validarNumeroTarjeta($numero) {
		if(strlen($numero) < 6) return false;
		$numero = strrev(preg_replace('/\\D+/', '', $numero));
		if($numero == '') return false;
		$total = 0;
		for($i = 0, $n = strlen($numero); $i < $n; $i++) {
			$total += $i % 2 ? 2 * $numero[$i] - ($numero[$i] > 4 ? 9 : 0) : $numero[$i];
		}
		return !($total % 10);
	}

	public function esValida() {
		return Tarjeta::validarNumeroTarjeta($this->numero);
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function tarjetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'tarjeta_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	/**
	 * Relación muchos a uno para la entidad a la que pertenece
	 * @return Entidad
	 */
	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
