<?php

namespace App\Http\Requests\General\ParametroInstitucional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditParametroInstitucionalRequest extends FormRequest
{
	private $parametro;

	public function __construct(Route $route) {
		$this->parametro = $route->obj;
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
		if($this->parametro->tipo_parametro == 'VALOR') {
			return [
				'valor'				=> 'bail|required|numeric',
			];
		}
		elseif($this->parametro->tipo_parametro == 'INDICADOR') {
			return [
				'indicador'			=> 'bail|required|boolean',
			];
		}
		else {
			return [
				'valor'				=> 'bail|required|numeric',
				'indicador'			=> 'bail|required|boolean',
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
