<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditSocioInformacionFinancieraRequest extends FormRequest
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
			'activos'				=> 'bail|nullable|integer',
			'pasivos'				=> 'bail|nullable|integer',
			'otros_ingresos'		=> 'bail|nullable|integer',
			'egresos_mensuales'		=> 'bail|nullable|integer',
			'fecha_corte'			=> 'bail|nullable|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('-1 day')),
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
		];
	}
}
