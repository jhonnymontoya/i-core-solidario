<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadDocumentacionRequest extends FormRequest
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
		$reglas =  [
			'nombre'									=> 'bail|required|string|min:5|max:50',
			'descripcion'								=> 'bail|required|string|min:10|max:1000',
			'es_exclusivo_de_socios'					=> 'bail|required|boolean',
			'esta_activa'								=> 'bail|required|boolean',
		];
		return $reglas;
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
		];
	}
}
