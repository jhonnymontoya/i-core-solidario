<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadAmortizacionRequest extends FormRequest
{
	private $modalidad;

	public function __construct(Route $route) {
		$this->modalidad = $route->obj;
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
		$reglas =  [
			'nombre'									=> 'bail|required|string|min:5|max:50',
			'descripcion'								=> 'bail|required|string|min:10|max:1000',
			'es_exclusivo_de_socios'					=> 'bail|required|boolean',
			'esta_activa'								=> 'bail|required|boolean',
			'tipo_cuota'								=> [
															'bail',
															'required',
															'string',
														],
			'periodicidades_admitidas'					=> 'bail|required|array',
			'acepta_cuotas_extraordinarias'				=> 'bail|required|boolean',
			'maximo_porcentaje_pago_extraordinario'		=> 'bail|nullable|required_if:acepta_cuotas_extraordinarias,1|numeric|min:0.1|max:100',
		];

		if($this->modalidad->tipo_tasa == 'SINTASA') {
			array_push($reglas['tipo_cuota'], 'in:CAPITAL');
		}
		else {
			array_push($reglas['tipo_cuota'], 'in:FIJA,CAPITAL');
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
			'tipo_cuota.in'					=> 'La cuota debe ser \'Fija capital\' cuando la modalidad no tiene tasa',
			'condicionPor.required_if'		=> 'La condición es requerida',
			'es_monto_condicionado.regex'	=> 'Imposible cambiar el método de monto, existen rangos asociados a la condición',
			'condicionPor.regex'			=> 'No se puede cambiar la condición, existen rangos asociados',
		];
	}

	public function attributes() {
		return [
		];
	}
}
