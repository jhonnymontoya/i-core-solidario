<?php

namespace App\Models\Creditos;

use App\Models\General\Tercero;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Codeudor extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.codeudores";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		//General
		'solicitud_credito_id',
		'tipo_garantia_id',
		'tercero_id',

		'es_permanente',
		'es_permanente_con_descubierto',
		'requiere_garantia_por_monto',
		'requiere_garantia_por_valor_descubierto',
		'valor_parametro_monto',
		'valor_parametro_descubierto',
		'valor_descubierto',

		//Codeudor
		'admite_codeudor_externo',
		'es_codeudor_externo',
		'valida_cupo_codeudor',
		'cupo_codeudor',
		'tiene_limite_obligaciones_codeudor',
		'parametro_limite_obligaciones_codeudor',
		'numero_obligaciones_codeudor',
		'tiene_limite_saldo_codeudas',
		'parametro_limite_saldo_codeudas',
		'valor_saldo_codeudas',
		'valida_antiguedad_codeudor',
		'parametro_antiguedad_codeudor',
		'valor_antiguedad_codeudor',
		'valida_calificacion_codeudor',
		'parametro_calificacion_minima_requerida_codeudor',
		'valor_calificacion_codeudor',
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
		'es_permanente'									=> 'boolean',
		'es_permanente_con_descubierto'					=> 'boolean',
		'requiere_garantia_por_monto'					=> 'boolean',
		'requiere_garantia_por_valor_descubierto'		=> 'boolean',
		'admite_codeudor_externo'						=> 'boolean',
		'valida_cupo_codeudor'							=> 'boolean',
		'tiene_limite_obligaciones_codeudor'			=> 'boolean',
		'tiene_limite_saldo_codeudas'					=> 'boolean',
		'valida_antiguedad_codeudor'					=> 'boolean',
		'valida_calificacion_codeudor'					=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	public function getCondicionAttribute() {
		if($this->attributes['es_permanente']) {
			return 'Permanente';
		}
		elseif($this->attributes['es_permanente_con_descubierto']) {
			return 'Con descubierto';
		}
		elseif($this->attributes['requiere_garantia_por_monto']) {
			return 'Por monto';
		}
		elseif($this->attributes['requiere_garantia_por_valor_descubierto']){
			return 'Por valor descubierto';
		}
		else {
			return '';
		}
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setEsPermanenteAttribute($value) {
		$this->attributes['es_permanente'] = empty($value) ? false : $value;
	}
	public function setEsPermanenteConDescubiertoAttribute($value) {
		$this->attributes['es_permanente_con_descubierto'] = empty($value) ? false : $value;
	}
	public function setRequiereGarantiaPorMontoAttribute($value) {
		$this->attributes['requiere_garantia_por_monto'] = empty($value) ? false : $value;
	}
	public function setRequiereGarantiaPorValorDescubiertoAttribute($value) {
		$this->attributes['requiere_garantia_por_valor_descubierto'] = empty($value) ? false : $value;
	}
	public function setAdmiteCodeudorExternoAttribute($value) {
		$this->attributes['admite_codeudor_externo'] = empty($value) ? false : $value;
	}
	public function setValidaCupoCodeudorAttribute($value) {
		$this->attributes['valida_cupo_codeudor'] = empty($value) ? false : $value;
	}
	public function setTieneLimiteObligacionesCodeudorAttribute($value) {
		$this->attributes['tiene_limite_obligaciones_codeudor'] = empty($value) ? false : $value;
	}
	public function setTieneLimiteSaldoCodeudasAttribute($value) {
		$this->attributes['tiene_limite_saldo_codeudas'] = empty($value) ? false : $value;
	}
	public function setValidaAntiguedadCodeudorAttribute($value) {
		$this->attributes['valida_antiguedad_codeudor'] = empty($value) ? false : $value;
	}
	public function setValidaCalificacionCodeudorAttribute($value) {
		$this->attributes['valida_calificacion_codeudor'] = empty($value) ? false : $value;
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

	/**
	 * Relaciones Muchos a uno
	 */

	public function solicitudCredito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}

	public function tipoGarantia() {
		return $this->belongsTo(TipoGarantia::class, 'tipo_garantia_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}