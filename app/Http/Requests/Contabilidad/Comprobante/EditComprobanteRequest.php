<?php

namespace App\Http\Requests\Contabilidad\Comprobante;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditComprobanteRequest extends FormRequest
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
			'descripcion' => 'bail|required|string|min:5|max:1000',
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
			'tipo_comprobante_id' => 'tipo comprobante',
		];
	}
}
