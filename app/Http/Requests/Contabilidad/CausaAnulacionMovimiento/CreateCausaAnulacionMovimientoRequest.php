<?php

namespace App\Http\Requests\Contabilidad\CausaAnulacionMovimiento;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateCausaAnulacionMovimientoRequest extends FormRequest
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
			'nombre'	=> [
							'bail',
							'required',
							'string',
							'min:5',
							'max:45',
							'unique:sqlsrv.contabilidad.causas_anulacion_movimiento,nombre,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
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
		];
	}

	public function attributes() {
		return [
		];
	}
}
