<?php

namespace App\Http\Requests\General\Tercero;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTerceroRequest extends FormRequest
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
		$reglas = array();
		if($this->tipo_tercero == 'NATURAL') {
			//Validaciones para campos de tercero natural
			$reglas = [
				'nTipoIdentificacion'		=> [
												'bail',
												'required',
												'integer',
												'exists:sqlsrv.general.tipos_identificacion,id,aplicacion,NATURAL,esta_activo,1,deleted_at,NULL'
											],
				'nNumeroIdentificacion'		=> [
												'bail',
												'required',
												'digits_between:4,15',
												'unique:sqlsrv.general.terceros,numero_identificacion,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
											],
				'nPrimerNombre'				=> 'bail|required|string|min:2|max:100',
				'nSegundoNombre'			=> 'bail|nullable|string|min:2|max:100',
				'nPrimerApellido'			=> 'bail|required|string|min:2|max:100',
				'nSegundoApellido'			=> 'bail|nullable|string|min:2|max:100',
			];
		}
		else {
			//Validaciones para campos de tercero jurídico
			$reglas = [
				'jTipoIdentificacion'		=> [
												'bail',
												'required',
												'integer',
												'exists:sqlsrv.general.tipos_identificacion,id,aplicacion,JURIDICA,esta_activo,1,deleted_at,NULL'
											],
				'jNumeroIdentificacion'		=> [
												'bail',
												'required',
												'digits_between:4,15',
												'unique:sqlsrv.general.terceros,numero_identificacion,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
											],
				'jRazonSocial'				=> 'bail|required|string|min:2|max:100',
				'jSigla'					=> 'bail|nullable|string|min:2|max:50',
			];
		}

		$reglas['tipo_tercero'] = 'bail|required|string|in:NATURAL,JURÍDICA';

		return $reglas;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'unique'						=> 'El tercero ya existe',
			'jRazonSocial.required'			=> 'La :attribute es requerida.',
			'jRazonSocial.min'				=> 'La :attribute debe tener al menos :min caracteres.',
			'jRazonSocial.max'				=> 'La :attribute no debe tener más de :max caracteres.',
			'jSigla.min'					=> 'La :attribute debe tener al menos :min caracteres.',
			'jSigla.max'					=> 'La :attribute no debe tener más de :max caracteres.',
			'nSegundoNombre.min'			=> ':attribute debe tener al menos :min caracteres.',
			'nSegundoNombre.max'			=> ':attribute no debe tener más de :max caracteres.',
		];
	}

	public function attributes() {
		return [
			'nTipoIdentificacion'			=> 'tipo de identificación',
			'nNumeroIdentificacion'			=> 'número de identificación',
			'nPrimerNombre'					=> 'primer nombre',
			'nSegundoNombre'				=> 'Otros nombre',
			'nPrimerApellido'				=> 'primer apellido',
			'nSegundoApellido'				=> 'segundo apellido',
			'jTipoIdentificacion'			=> 'tipo de identificación',
			'jNumeroIdentificacion'			=> 'número de identificación',
			'jRazonSocial'					=> 'razón social',
			'jSigla'						=> 'sigla',
		];
	}
}
