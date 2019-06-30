<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditSocioInformacionTarjetaCreditoRequest extends FormRequest
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
			'franquicia'	=> [
								'bail',
								'nullable',
								'exists:sqlsrv.tesoreria.franquicias,id,deleted_at,NULL'
							],
			'banco'			=> [
								'bail',
								'nullable',
								'required_with:anio,mes,cupo,saldo',
								'exists:sqlsrv.tesoreria.bancos,id,esta_activo,1,deleted_at,NULL'
							],
			'anio'			=> [
								'bail',
								'nullable',
								'required_with:banco,mes,cupo,saldo',
								'integer',
								'min:' . date('Y'),
								'digits:4'
							],
			'mes'			=> [
								'bail',
								'nullable',
								'required_with:banco,anio,cupo,saldo',
								'integer',
								'min:1',
								'max:12',
								'digits_between:1,2'
							],
			'cupo'			=> 'bail|nullable|integer',
			'saldo'			=> 'bail|nullable|integer',
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
			'anio'		=> 'año',
		];
	}
}
