<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditSocioRequest extends FormRequest
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
			'primer_nombre'					=> 'bail|required|string|min:2|max:100',
			'segundo_nombre'				=> 'bail|nullable|string|min:2|max:100',
			'primer_apellido'				=> 'bail|required|string|min:2|max:100',
			'segundo_apellido'				=> 'bail|nullable|string|min:2|max:100',
			'fecha_nacimiento'				=> 'bail|required|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('-15 year')),
			'ciudad_nacimiento'				=> [
												'bail',
												'nullable',
												'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
											],
			'fecha_exp_doc_id'				=> 'bail|nullable|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('-1 day')),
			'ciudad_exp_doc_id'				=> [
												'bail',
												'nullable',
												'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
											],
			'sexo'							=> [
												'bail',
												'required',
												'exists:sqlsrv.general.sexos,id,deleted_at,NULL'
											],
			'estado_civil'					=> [
												'bail',
												'nullable',
												'exists:sqlsrv.socios.estados_civiles,id,deleted_at,NULL'
											],
			'mujer_cabeza_familia'			=> 'bail|required|boolean',
			'transferencia_banco_id'		=> [
												'bail',
												'nullable',
												'required_with:transferencia_numero_cuenta',
												'exists:sqlsrv.tesoreria.bancos,id,esta_activo,1,deleted_at,NULL'
											],
			'transferencia_tipo_cuenta'		=> 'bail|nullable|in:AHORROS,CORRIENTE',
			'transferencia_numero_cuenta'	=> 'bail|nullable|required_with:transferencia_banco_id|string|digits_between:5,45',
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
