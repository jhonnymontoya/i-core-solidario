<?php

namespace App\Http\Requests\Contabilidad\Comprobante;

use Illuminate\Foundation\Http\FormRequest;

class UploadDetalleMovimientosRequest extends FormRequest
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
			'archivo'		=> 'bail|required|file|max:10240|mimetypes:text/plain',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'archivo.mimetypes'		=> 'El :attribute debe ser un CSV válido.',
			'archivo.min'			=> 'El :attribute no puede estar vacio.',
		];
	}

	public function attributes() {
		return [
		];
	}
}
