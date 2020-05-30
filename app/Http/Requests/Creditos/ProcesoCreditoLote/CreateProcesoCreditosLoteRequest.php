<?php

namespace App\Http\Requests\Creditos\ProcesoCreditoLote;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateProcesoCreditosLoteRequest extends FormRequest
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
			'fecha_proceso'					=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7',
			'modalidad_credito_id'			=> [
												'bail',
												'required',
												'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $entidad->id . ',esta_activa,1,deleted_at,NULL',
											],
			'descripcion'					=> 'bail|nullable|string|max:1000',
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
			'referencia'					=> 'bail|nullable|string|max:50',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'fecha_proceso.modulocerrado' => 'Módulo de cartera cerrado para la fecha',
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
