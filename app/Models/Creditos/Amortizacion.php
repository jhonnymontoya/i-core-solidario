<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Amortizacion extends Model
{
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.amortizaciones";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'obligacion_id',
		'numero_cuota',
		'naturaleza_cuota', //EXTRAORDINARIA, ORDINARIA
		'forma_pago', //NOMINA, PRIMA, CAJA
		'fecha_cuota',
		'abono_capital',
		'abono_intereses',
		'abono_seguro_cartera',
		'total_cuota',
		'nuevo_saldo_capital',
		'estado_cuota', //PAGA, PARCIAL, PENDIENTE O NULO
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_cuota',
		'created_at',
		'updated_at',
	];

	/**
	 * Atributos que deben ser convertidos a tipos nativos.
	 *
	 * @var array
	 */
	protected $casts = [
		'abono_capital'				=> 'integer',
		'abono_intereses'			=> 'integer',
		'abono_seguro_cartera'		=> 'integer',
		'total_cuota'				=> 'integer',
		'nuevo_saldo_capital'		=> 'integer',
	];

	/**
	 * Getters personalizados
	 */

	/**
	 * Setters Personalizados
	 */

	public function setFechaCuotaAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_cuota'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_cuota'] = null;
		}
	}
	
	/**
	 * Scopes
	 */
	
	public function scopeEstadoCuota($query, $value) {
		/*TODO*/
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

	public function obligacion() {
		return $this->belongsTo(SolicitudCredito::class, 'obligacion_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}
