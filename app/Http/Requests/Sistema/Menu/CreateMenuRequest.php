<?php

namespace App\Http\Requests\Sistema\Menu;

use Illuminate\Foundation\Http\FormRequest;

class CreateMenuRequest extends FormRequest
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
			'padre'			=> [
								'bail',
								'nullable',
								'exists:sqlsrv.sistema.menus,id,deleted_at,NULL'
							],
			'nombre'		=> 'bail|required|string|min:2|max:100',
			'ruta'			=> 'bail|nullable|string|min:2|max:100',
			'icono'			=> 'bail|nullable|string|min:2|max:45',
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
			'padre'		=> 'menú padre',
		];
	}
}
