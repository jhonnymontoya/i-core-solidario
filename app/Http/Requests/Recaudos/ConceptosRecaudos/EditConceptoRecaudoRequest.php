<?php

namespace App\Http\Requests\Recaudos\ConceptosRecaudos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Carbon\Carbon;

class EditConceptoRecaudoRequest extends FormRequest
{
	private $obj;

	public function __construct(Route $route) {
		$this->obj = $route->obj;
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
		return [
			'codigo'					=> [
											'bail',
											'required',
											'string',
											'min:2',
											'max:10',
											'unique:sqlsrv.recaudos.conceptos_recaudos,codigo,' . $this->obj->id . ',id',
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
		];
	}
}
