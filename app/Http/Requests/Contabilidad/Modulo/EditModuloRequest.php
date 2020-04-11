<?php

namespace App\Http\Requests\Contabilidad\Modulo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModuloRequest extends FormRequest
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
		return [
			'nombre' => [
				'bail',
				'required',
				'string',
				'min:2',
				'max:100',
				'unique:sqlsrv.contabilidad.modulos,nombre,' . $this->obj->id . ',id,deleted_at,NULL'
			],
			'activo' => 'bail|required|boolean',
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
		];
	}
}
