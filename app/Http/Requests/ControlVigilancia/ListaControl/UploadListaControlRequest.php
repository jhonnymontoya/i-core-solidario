<?php

namespace App\Http\Requests\ControlVigilancia\ListaControl;

use Illuminate\Foundation\Http\FormRequest;

class UploadListaControlRequest extends FormRequest
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
	public function rules()
	{
		return [
			'archivo'		=> [
				'bail',
				'required',
				'file',
				'max:51200',
				'mimetypes:application/xml,text/xml'
			]
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'archivo.mimetypes'		=> 'El :attribute debe ser un XML válido.',
			'archivo.min'			=> 'El :attribute no puede estar vacio.',
		];
	}

	public function attributes() {
		return [
		];
	}
}
