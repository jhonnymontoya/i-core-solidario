<?php

namespace App\Http\Requests\Sistema\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
class EditProfileRequest extends FormRequest
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
		$usuario = Auth::user();
		return [
			'avatar'					=> 'bail|nullable',
			'tipo_identificacion_id'	=> [
											'bail',
											'required',
											'exists:sqlsrv.general.tipos_identificacion,id,deleted_at,NULL'
										],
			'identificacion'			=> [
											'bail',
											'required',
											'integer',
											'digits_between:5,30',
											'unique:sqlsrv.sistema.usuarios,identificacion,' . $usuario->id . ',id,deleted_at,NULL'
										],
			'password'					=> 'bail|nullable|string|min:6|max:20',
			'confirmar_password'		=> 'bail|same:password',
			'primer_nombre'				=> 'bail|required|string|min:2|max:100',
			'segundo_nombre'			=> 'bail|nullable|string|min:2|max:100',
			'primer_apellido'			=> 'bail|required|string|min:2|max:100',
			'segundo_apellido'			=> 'bail|nullable|string|min:2|max:100',
			'email'						=> [
											'bail',
											'required',
											'email',
											'min:3',
											'max:100',
											'unique:sqlsrv.sistema.usuarios,email,' . $usuario->id . ',id,deleted_at,NULL'
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
			'password.min'				=> 'La :attribute no cumple con la longitud requerida',
			'password.max'				=> 'La :attribute no debe contener más de :max caracteres',
			'confirmar_password.same'	=> 'El campo :attribute y :other deben ser iguales',
		];
	}

	public function attributes() {
		return [
			'password'				=> 'contraseña',
			'confirmar_password'	=> 'confirmar contraseña'
		];
	}
}
