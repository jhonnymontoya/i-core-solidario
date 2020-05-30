<?php

namespace App\Http\Requests\Ahorros\SDAT;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateSDATRequest extends FormRequest
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
		return [
			'tipo_sdat'	=> [
				'bail',
				'required',
				'integer',
				'min:1',
				'exists:sqlsrv.ahorros.tipos_sdat,id,entidad_id,' . $this->getEntidad()->id
			],
			'socio'		=> [
				'bail',
				'required',
				'integer',
				'min:1',
				'exists:sqlsrv.socios.socios,id'
			],
			'valor'		=> 'bail|required|integer|min:1',
			'fecha'		=> 'bail|required|date_format:"d/m/Y"|modulocerrado:6',
			'plazo'		=> 'bail|required|integer|min:1',
			'radicar'	=> 'bail|nullable|string|in:Radicar'
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'fecha.modulocerrado' => 'Módulo de ahorros cerrado para la fecha de radicación.'
		];
	}

	public function attributes() {
		return [
		];
	}
}
