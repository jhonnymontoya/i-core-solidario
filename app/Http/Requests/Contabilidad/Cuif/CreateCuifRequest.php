<?php

namespace App\Http\Requests\Contabilidad\Cuif;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateCuifRequest extends FormRequest
{

	use ICoreTrait;

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
									'regex:/^([1-9]([1-9](([0-9][1-9])|([1-9][0-9])){0,8})?)?$/',
									'unique:sqlsrv.contabilidad.cuifs,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'nombre'			=> 'bail|required|string|min:4|max:255',
			'naturaleza'		=> 'bail|required|in:DÉBITO,CRÉDITO',
			'negativo'			=> 'bail|required|boolean',
			'resultado'			=> 'bail|required|boolean',
			'ordent'			=> 'bail|required|boolean',
			'orden' 			=> 'bail|nullable|exists:sqlsrv.contabilidad.cuifs,codigo',
			'comentario'		=> 'bail|nullable|string|max:1000',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'codigo.regex'		=> 'El formato del :attribute no es correcto'
		];
	}

	public function attributes() {
		return [
		];
	}
}
