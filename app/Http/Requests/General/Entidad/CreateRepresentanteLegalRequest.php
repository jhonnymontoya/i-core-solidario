<?php

namespace App\Http\Requests\General\Entidad;

use Illuminate\Foundation\Http\FormRequest;

class CreateRepresentanteLegalRequest extends FormRequest
{
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
			'legal_entidad'					=> [
												'bail',
												'required',
												'exists:sqlsrv.general.entidades,id,deleted_at,NULL',
											],
			'legal_tercero'					=> [
												'bail',
												'required',
												'exists:sqlsrv.general.terceros,id,esta_activo,1,deleted_at,NULL',
											],
			'legal_calidad'					=> 'bail|required|in:PRINCIPAL,SUPLENTE',
			'legal_fecha_nombramiento'		=> 'bail|required|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('+1 day')),
			'legal_periodo'					=> 'bail|required|integer|min:1|max:5'
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'legal_tercero.igual'	=> 'El socio no se encuentra ACTIVO', 
		];
	}

	public function attributes() {
		return [
		];
	}
}
