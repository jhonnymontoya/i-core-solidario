<?php

namespace App\Http\Requests\General\CategoriaImagen;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoriaImagenRequest extends FormRequest
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
			'nombre'		=> [
								'required',
								'min:4',
								'max:100',
								'unique:sqlsrv.general.categorias_imagen,nombre,NULL,id,deleted_at,NULL'
							],
			'descripcion'	=> 'min:5|max:250|string|nullable',
			'ancho'			=> 'required|integer|min:10|max:500',
			'alto'			=> 'required|integer|min:10|max:500',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'descripcion.min' => 'La :attribute debe tener al menos :min caracteres.',
			'descripcion.max' => 'La :attribute no debe tener más de :max caracteres.',
		];
	}

	public function attributes() {
		return [
			'ancho'		=> 'ancho de la imagen',
			'alto'		=> 'alto de la imagen',
		];
	}
}
