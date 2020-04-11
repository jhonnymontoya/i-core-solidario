<?php

namespace App\Http\Requests\Creditos\Modalidad;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateModalidadRequest extends FormRequest
{

	use FonadminTrait;

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
		$entidad = $this->getEntidad();
		return [
			'codigo'			=> [
									'bail',
									'required',
									'string',
									'min:2',
									'max:5',
									'unique:sqlsrv.creditos.modalidades,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'nombre'			=> 'bail|required|string|min:5|max:50',
			'descripcion'		=> 'bail|required|string|min:10|max:1000',
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
