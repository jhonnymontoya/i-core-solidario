<?php

namespace App\Http\Requests\Creditos\CobroAdministrativo;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateCobroAdministrativoRequest extends FormRequest
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
									'string',
									'min:2',
									'max:5',
									'unique:sqlsrv.creditos.cobros_administrativos,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'nombre'			=> 'bail|required|string|min:3|max:50',
			'efecto'			=> 'bail|required|string|in:DEDUCCIONCREDITO,ADICIONCREDITO',
			'destino_cuif_id'	=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL'
								],
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'destino_cuif_id.required'	=> 'La :attribute es requerida.'
		];
	}

	public function attributes() {
		return [
			'destino_cuif_id'	=> 'cuenta destino'
		];
	}
}
