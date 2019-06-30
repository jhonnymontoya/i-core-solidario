<?php

namespace App\Http\Requests\Creditos\SeguroCartera;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditSeguroCarteraRequest extends FormRequest
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
			'nombre'					=> 'bail|required|string|min:2|max:150',
			'base_prima'				=> 'bail|required|string|in:SALDO,VALORINICIAL',
			'aseguradora_tercero_id'	=> [
											'bail',
											'required',
											'numeric',
											'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL'
										],
			'tasa_mes'					=> 'bail|required|numeric|min:0.0001',
			'esta_activo'				=> 'bail|required|boolean',
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
