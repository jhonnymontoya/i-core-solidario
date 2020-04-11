<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditSocioInformacionObligacionFinancieraRequest extends FormRequest
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
			'banco'					=> [
										'bail',
										'nullable',
										'required_with:monto,tasa_mes_vencido,plazo,fecha_inicial',
										'exists:sqlsrv.tesoreria.bancos,id,esta_activo,1,deleted_at,NULL'
									],
			'monto'					=> 'bail|required|integer',
			'tasa_mes_vencido'		=> 'bail|nullable|numeric|min:0.1|max:100',
			'plazo'					=> 'bail|nullable|integer|digits_between:1,3',
			'fecha_inicial'			=> 'bail|nullable|date_format:"d/m/Y"',
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
