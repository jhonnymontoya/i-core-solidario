<?php

namespace App\Http\Requests\General\TipoIdentificacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoIdentificacionRequest extends FormRequest
{
	private $route;

	public function __construct(Route $route) {
		$this->route = $route;
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
			'aplicacion'	=> 'bail|required|in:NATURAL,JURÍDICA',
			'codigo'		=> [
								'bail',
								'required',
								'min:1',
								'max:3',
								'string',
								'unique:sqlsrv.general.tipos_identificacion,codigo,' . $this->route->obj->id . ',id,deleted_at,NULL'
							],
			'nombre'		=> 'bail|required|min:3|max:50|string',
			'esta_activo'	=> 'bail|required|boolean',
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
			'aplicacion'		=> 'tipo de persona',
			'esta_activo'		=> 'estado',
		];
	}
}
