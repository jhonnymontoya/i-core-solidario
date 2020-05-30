<?php

namespace App\Http\Requests\Contabilidad\Comprobante;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateComprobanteRequest extends FormRequest
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
			'tipo_comprobante_id' => [
				'bail',
				'required',
				'exists:sqlsrv.contabilidad.tipos_comprobantes,id,entidad_id,' .
				$entidad->id . ',deleted_at,NULL',
			],
			'fecha_movimiento' => ['
				bail',
				'required',
				'date_format:"d/m/Y"',
				'modulocerrado:2'
			],
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
			"fecha_movimiento.modulocerrado" => "Módulo de contabilidad cerrado para la fecha"
		];
	}

	public function attributes() {
		return [
			'tipo_comprobante_id'		=> 'tipo comprobante',
		];
	}
}
