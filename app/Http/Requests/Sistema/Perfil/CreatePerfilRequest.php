<?php

namespace App\Http\Requests\Sistema\Perfil;

use Illuminate\Foundation\Http\FormRequest;

class CreatePerfilRequest extends FormRequest
{

	public function __construct() {
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
			'entidad_id'		=> [
									'bail',
									'required',
									'exists:sqlsrv.general.entidades,id,deleted_at,NULL'
								],
			'nombre'			=> [
									'bail',
									'required',
									'string',
									'min:2',
									'max:45',
									'unique:sqlsrv.sistema.perfiles,nombre,NULL,id,entidad_id,' . $this->entidad_id . ',deleted_at,NULL'
								],
			'descripcion'		=> 'bail|nullable|string|max:1000',
			'menus'				=> [
									'bail',
									'required',
									'array',
									'exists:sqlsrv.sistema.menus,id,deleted_at,NULL'
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
			'nombre.unique'		=> 'El nombre del perfil ya está en uso para la entidad seleccionada.',
		];
	}

	public function attributes() {
		return [
			'entidad_id'		=> 'entidad',
		];
	}
}
