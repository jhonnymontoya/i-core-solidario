<?php

namespace App\Http\Requests\Contabilidad\Comprobante;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditDetalleRequest extends FormRequest
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
		$r = ",esta_activo,1,deleted_at,NULL";
		$cuenta = "exists:sqlsrv.contabilidad.cuifs,id,entidad_id,%s," .
			"tipo_cuenta,AUXILIAR%s";
		$tercero = "exists:sqlsrv.general.terceros,id,entidad_id,%s%s";
		return [
			'cuenta' => [
				'bail',
				'required',
				sprintf($cuenta, $entidad->id, $r)
			],
			'tercero' => [
				'bail',
				'required',
				sprintf($tercero, $entidad->id, $r)
			],
			'debito' => 'bail|nullable|integer',
			'credito' => 'bail|nullable|required_without:debito|integer',
			'referencia' => [
				'bail',
				'nullable',
				'regex:/^[a-zA-Z0-9-+*\/.,;:]*$/',
			],
		];
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if (!empty($this->debito) && !empty($this->credito)) {
				$validator->errors()->add(
					'credito',
					'Sólo se admite un valor en débito o crédito'
				);
			}
		});
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'cuenta.exists' => 'La :attribute seleccionada es inválida',
			'cuenta.required' => 'La :attribute es requerida',
			'referencia.regex' => 'La :attribute contiene elementos invalidos, solo (letras, números y (-+*/.,;:) sin espacios)',
			'credito.required_without' => 'Se requiere un valor en débito o crédito',
		];
	}

	public function attributes() {
		return [
		];
	}
}
