<?php

namespace App\Http\Requests\Contabilidad\Impuesto;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;

class CreateConceptoRequest extends FormRequest
{
	use FonadminTrait;

	private $impuesto;

	public function __construct(Route $route) {
		$this->impuesto = $route->obj;
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
		$entidad = $this->getEntidad();

		$cuenta = "exists:sqlsrv.contabilidad.cuifs,id,entidad_id,%s,tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,5,deleted_at,NULL";
		$cuenta = sprintf($cuenta, $entidad->id);

		return [
			'nombreConcepto' => [
				'bail',
				'required',
				'string',
				'min:4',
				'max:250'
			],
			'cuenta' => [
				'bail',
				'required',
				'integer',
				$cuenta
			],
			'tasa' => 'bail|required|numeric|min:0|max:100'
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'cuenta.required' => 'La :attribute es requerida.',
			'cuenta.exists' => 'La :attribute no es válida.',
			'tasa.required' => 'La :attribute es requerida.',
			'tasa.min' => 'La :attribute debe ser mínimo de :min.',
			'tasa.max' => 'La :attribute debe ser máximo de :max.',
		];
	}

	public function attributes() {
		return [
			'cuenta' => 'cuenta destino',
		];
	}
}
