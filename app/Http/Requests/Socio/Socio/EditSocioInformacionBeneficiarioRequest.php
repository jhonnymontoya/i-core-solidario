<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;
class EditSocioInformacionBeneficiarioRequest extends FormRequest
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
			'tipo_identificacion'	=> [
										'bail',
										'required',
										'exists:sqlsrv.general.tipos_identificacion,id,aplicacion,NATURAL,deleted_at,NULL',
									],
			'identificacion'		=> 'bail|required|digits_between:4,15',
			'nombres'				=> 'bail|required|string|min:2|max:100',
			'apellidos'				=> 'bail|required|string|min:2|max:100',
			'parentesco'			=> [
										'bail',
										'required',
										'exists:sqlsrv.socios.parentescos,id,deleted_at,NULL'
									],
			'beneficio'				=> 'bail|required|integer|min:1|max:100',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'tipo_identificacion.igual'			=> 'El campo :attribute seleccionado no es válido',
			'beneficio.min'						=> 'El campo :attribute debe ser de al menos :min',
			'beneficio.max'						=> 'El campo :attribute debe ser máximo de :max',
			'identificacion.required'			=> 'El campo :attribute es requerido',
			'nombres.required'					=> 'El campo :attribute es requerido',
			'apellidos.required'				=> 'El campo :attribute es requerido',
		];
	}

	public function attributes() {
		return [
			'tipo_identificacion'				=> 'tipo de identificación',
		];
	}
}
