<?php

namespace App\Http\Requests\Creditos\CUpoCredito;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditCupoCreditoSDATRequest extends FormRequest
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
			'sdat'					=> [
				'bail',
				'required',
				'integer',
				'min:1',
				'exists:sqlsrv.ahorros.tipos_sdat,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL'
			],
			'apalancamiento_cupo'	=> 'bail|required|numeric|min:0',
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
