<?php

namespace App\Http\Requests\Creditos\ParametrosDeterioroIndividual;

use Illuminate\Foundation\Http\FormRequest;

class SelectParametrosDeterioroIndividualRequest extends FormRequest
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
		];
	}
}
