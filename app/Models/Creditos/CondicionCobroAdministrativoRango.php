<?php

namespace App\Models\Creditos;

use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondicionCobroAdministrativoRango extends Model
{
	use FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.condiciones_cobros_administrativos_rangos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'cobro_administrativo_id',
		'condicion_desde',
		'condicion_hasta',
		'base_cobro', //VALORCREDITO, VAORDESCUBIERTO
		'factor_calculo', //VALORFIJO, PORCENTAJEBASE
		'valor',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at'
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
	 * Funciones
	 */

	public function paraMostrar() {
		$cobro = $this->cobroAdministrativo;
		$desde = $this->condicion_desde;
		$hasta = $this->condicion_hasta;
		$valor = $this->valor;
		if($cobro->condicion == 'MONTO') {
			$desde = '$' . number_format($desde);
			$hasta = '$' . number_format($hasta);
		}
		$base = $this->base_cobro == 'VALORCREDITO' ? 'Valor crédito' : 'Valor descubierto';
		$factor = $this->factor_calculo == 'VALORFIJO' ? 'Valor fijo' : 'Porcentaje base';
		if($this->factor_calculo == 'VALORFIJO') {
			$valor = '$' . number_format($valor);
		}
		else {
			$valor = number_format($valor, 2) . '%';
		}
		return [
			'id'						=> $this->id,
			'cobro_administrativo_id'	=> $this->cobro_administrativo_id,
			'condicion_desde'			=> $desde,
			'condicion_hasta'			=> $hasta,
			'base_cobro'				=> $base,
			'factor_calculo'			=> $factor,
			'valor'						=> $valor,
		];
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

	public function cobroAdministrativo() {
		return $this->belongsTo(CobroAdministrativo::class, 'cobro_administrativo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
