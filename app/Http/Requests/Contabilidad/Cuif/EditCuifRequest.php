<?php

namespace App\Http\Requests\Contabilidad\Cuif;

use Illuminate\Foundation\Http\FormRequest;

class EditCuifRequest extends FormRequest
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
			'nombre'			=> 'bail|required|string|min:4|max:255',
			'naturaleza'		=> 'bail|required|in:DÉBITO,CRÉDITO',
			'negativo'			=> 'bail|required|boolean',
			'comentario'		=> 'bail|nullable|string|max:1000',
			'esta_activo'		=> 'bail|required|boolean',
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
			'esta_activo'	=> 'estado',
		];
	}
}
