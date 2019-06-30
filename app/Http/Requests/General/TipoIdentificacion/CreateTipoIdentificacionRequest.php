<?php

namespace App\Http\Requests\General\TipoIdentificacion;

use Illuminate\Foundation\Http\FormRequest;

class CreateTipoIdentificacionRequest extends FormRequest
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
			'aplicacion'	=> 'required|in:NATURAL,JURÍDICA',
			'codigo'		=> [
								'required',
								'min:1',
								'max:3',
								'string',
								'unique:sqlsrv.general.tipos_identificacion,codigo,NULL,id,deleted_at,NULL'
							],
			'nombre'		=> 'required|min:3|max:50|string',
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
			'aplicacion'		=> 'tipo de persona',
		];
	}
}
