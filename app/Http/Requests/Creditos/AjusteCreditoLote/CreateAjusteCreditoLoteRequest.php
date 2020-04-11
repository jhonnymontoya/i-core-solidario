<?php

namespace App\Http\Requests\Creditos\AjusteCreditoLote;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateAjusteCreditoLoteRequest extends FormRequest
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
			'fecha_proceso'					=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7',
			'descripcion'					=> 'bail|nullable|string|max:2000',
			'contrapartida_cuif_id'			=> [
												'bail',
												'required',
												'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL',
											],
			'contrapartida_tercero_id'		=> [
												'bail',
												'required',
												'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
											],
			'referencia'					=> 'bail|nullable|string|max:100',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			"fecha_proceso.modulocerrado" => "Módulo de cartera cerrado para la fecha de proceso"
		];
	}

	public function attributes() {
		return [
			'modalidad_credito_id'		=> 'modalidad',
			'contrapartida_cuif_id'		=> 'cuenta',
			'contrapartida_tercero_id'	=> 'tercdero',
		];
	}
}
