<?php

namespace App\Http\Requests\Creditos\ParametrosDeterioroIndividual;

use App\Models\Creditos\ParametroDeterioroIndividual;
use Illuminate\Foundation\Http\FormRequest;

class CreateParametroDeterioroIndividualRequest extends FormRequest
{
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
		return [
			'tipo_cartera'		=> 'bail|required|string|in:CONSUMO,VIVIENDA,COMERCIAL,MICROCREDITO',
			'clase'				=> 'bail|required|string|in:CAPITAL,INTERES',
			'dias_desde'		=> 'bail|required|integer|min:0',
			'dias_hasta'		=> 'bail|required|integer|min:0',
			'deterioro'			=> 'bail|required|integer|min:0'
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
		];
	}

	public function attributes() {
		return [
			'dias_desde'		=> 'valor desde',
			'dias_hasta'		=> 'valor hasta',
		];
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if(is_null($this->dias_desde) || is_null($this->dias_hasta))return null;
			if(!is_numeric($this->dias_desde) || !is_numeric($this->dias_hasta))return null;
			if($this->dias_desde >= $this->dias_hasta) {
				$validator->errors()->add('dias_hasta', 'Rango de días inconsistente');
				return;
			}
			if(ParametroDeterioroIndividual::contenidoEnParametro($this->tipo_cartera, $this->clase, $this->dias_desde)) {
				$validator->errors()->add('dias_desde', 'Valor contenido en otro rango');
				return;
			}
			if(ParametroDeterioroIndividual::contenidoEnParametro($this->tipo_cartera, $this->clase, $this->dias_hasta)) {
				$validator->errors()->add('dias_hasta', 'Valor contenido en otro rango');
				return;
			}
		});
	}
}
