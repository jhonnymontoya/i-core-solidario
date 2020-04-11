<?php

namespace App\Http\Requests\Creditos\SolicitudCredito;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Carbon\Carbon;

class EditSolicitudCreditoRequest extends FormRequest
{
	private $solicitud;

	public function __construct(Route $route) {
		$this->solicitud = $route->obj;
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$reglas = [
			'valor_credito'								=> 'bail|required|numeric|min:1',
			'plazo'										=> 'bail|required|integer|min:1|max:1000',
			'forma_pago'								=> 'bail|required|string|in:NOMINA,PRIMA,CAJA',
			'periodicidad'								=> 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL',
			'fecha_primer_pago'							=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7',
			'observaciones'								=> 'bail|nullable|string|max:1000',
		];

		if($this->solicitud->modalidadCredito->tipo_cuota == 'CAPITAL') {
			$reglas['fecha_primer_pago_intereses'] = ['bail', 'required', 'date_format:"d/m/Y"', 'before_or_equal:fecha_primer_pago', 'modulocerrado:7'];
			if($this->plazo == 1) {
				array_push($reglas['fecha_primer_pago_intereses'], 'same:fecha_primer_pago');
			}
		}
		return $reglas;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'periodicidad.required'			=> 'La :attribute es requerida',
			'fecha_primer_pago_intereses.before_or_equal' => 'El primer pago de intereses no puede ser posterior al primer pago de capital',
			'fecha_primer_pago_intereses.same'	=> 'A una cuota la fecha de primer pago y la fecha primer pago intereses deben ser iguales',
			'fecha_solicitud.modulocerrado' => 'Módulo de cartera cerrado para la fecha',
			'fecha_primer_pago.modulocerrado' => 'Módulo de cartera cerrado para la fecha',
			'fecha_primer_pago_intereses.modulocerrado' => 'Módulo de cartera cerrado para la fecha',
			'plazo.max'	=> 'El :attribute de la simulación no es coherente, se espera el número de cuotas'
		];
	}

	public function attributes() {
		return [
		];
	}
}
