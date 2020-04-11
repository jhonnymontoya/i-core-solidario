<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditAfiliacionRequest extends FormRequest
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
			'fecha_afiliacion'			=> 'bail|required|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('+1 month')),
			'fecha_antiguedad'			=> 'bail|required|date_format:"d/m/Y"|before_or_equal:fecha_afiliacion',
			'referido'					=> [
											'bail',
											'nullable',
											'exists:sqlsrv.general.terceros,id,esta_activo,1,deleted_at,NULL'
										],
			'comentario'				=> 'bail|nullable|string|max:1000',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'fecha_afiliacion.before'	=> 'El campo :attribute no debe ser mayor un mes de la fecha actual.'
		];
	}

	public function attributes() {
		return [
		];
	}
}
