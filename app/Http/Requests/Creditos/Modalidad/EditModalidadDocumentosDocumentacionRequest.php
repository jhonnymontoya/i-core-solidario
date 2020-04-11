<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadDocumentosDocumentacionRequest extends FormRequest
{

	private $modalidad;

	public function __construct(Route $route) {
		$this->modalidad = $route->obj;
	}

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
			'documento'			=> 'bail|required|string|min:5|max:100',
			'obligatorio'		=> 'bail|required|boolean',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'documento.required'			=> 'El campo :attribute es requerido',
			'obligatorio.required'			=> 'El campo :attribute es requerido',
		];
	}

	public function attributes() {
		return [
		];
	}
}
