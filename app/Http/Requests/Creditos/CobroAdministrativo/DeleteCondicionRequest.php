<?php

namespace App\Http\Requests\Creditos\CobroAdministrativo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class DeleteCondicionRequest extends FormRequest
{
	private $cobro;

	public function __construct(Route $route) {
		$this->cobro = $route->obj; 
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
	public function rules()	{
		return [
			'condicion' => [
							'bail',
							'required',
							'integer',
							'min:1',
							'exists:sqlsrv.creditos.condiciones_cobros_administrativos_rangos,id,cobro_administrativo_id,' . $this->cobro->id,
						]
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'condicion.exists'	=> 'La condición no es válida.'
		];
	}

	public function attributes() {
		return [
		];
	}
}
