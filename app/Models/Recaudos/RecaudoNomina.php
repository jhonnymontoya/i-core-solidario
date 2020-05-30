<?php

namespace App\Models\Recaudos;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Tercero;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;

class RecaudoNomina extends Model
{
	use ICoreTrait, ICoreModelTrait;
    /**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "recaudos.recaudos_nomina";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'control_proceso_id',
		'tercero_id',
		'modalidad_id',
		'solicitud_credito_id',
		'concepto_recaudo_id',
		'tipo_recaudo', //AHORRO, CRÉDITO
		'forma_pago', //NOMINA, PRIMA
		'capital_generado',
		'intereses_generado',
		'seguro_generado',
		'capital_aplicado',
		'intereses_aplicado',
		'seguro_aplicado',
		'capital_ajustado',
		'intereses_ajustado',
		'seguro_ajustado',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
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
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relaciones Muchos a uno
	 */

	public function controlProceso() {
		return $this->belongsTo(ControlProceso::class, 'control_proceso_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function modalidadAhorro() {
		return $this->belongsTo(ModalidadAhorro::class, 'modalidad_id', 'id');
	}

	public function solcitudCredito() {
		return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
	}

	public function conceptoRecaudo() {
		return $this->belongsTo(ConceptoRecaudos::class, 'concepto_recaudo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
