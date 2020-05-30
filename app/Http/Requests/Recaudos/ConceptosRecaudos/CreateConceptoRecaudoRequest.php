<?php

namespace App\Http\Requests\Recaudos\ConceptosRecaudos;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateConceptoRecaudoRequest extends FormRequest
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
			'pagaduria_id'				=> [
											'bail',
											'required',
											'exists:sqlsrv.recaudos.pagadurias,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
										],
			'codigo'					=> [
											'bail',
											'required',
											'string',
											'min:2',
											'max:10',
											'unique:sqlsrv.recaudos.conceptos_recaudos,codigo,NULL,id,pagaduria_id,' . $this->pagaduria_id,
										],
			'nombre'					=> 'bail|required|string|min:3|max:100',
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
			'pagaduria_id'			=> 'pagaduria',
		];
	}
}
