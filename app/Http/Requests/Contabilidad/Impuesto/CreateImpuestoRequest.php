<?php

namespace App\Http\Requests\Contabilidad\Impuesto;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateImpuestoRequest extends FormRequest
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
		$nombreUnico = 'unique:sqlsrv.contabilidad.impuestos,nombre,NULL,id,entidad_id,%s,deleted_at,NULL';
		$nombreUnico = sprintf($nombreUnico, $entidad->id);
		return [
			'nombre' => [
				'bail',
				'required',
				'string',
				'min:3',
				'max:250',
				$nombreUnico
			],
			"tipo" => "bail|required|string|in:NACIONAL,DISTRITAL,REGIONAL"
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
