<?php

namespace App\Http\Requests\General\Indicador;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateIndicadorRequest extends FormRequest
{

	private $obj;

	public function __construct(Route $route) {
		$this->obj = $route->obj;
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
		if($this->obj->indicadores->count()) {
			return [
				'fecha_inicio'		=> 'bail|required|date_format:"d/m/Y"',
				'valor'				=> 'bail|required|numeric',
			];
		}
		else {
			return [
				'fecha_inicio'		=> 'bail|required|date_format:"d/m/Y"',
				'valor'				=> 'bail|required|numeric',
			];
		}
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
