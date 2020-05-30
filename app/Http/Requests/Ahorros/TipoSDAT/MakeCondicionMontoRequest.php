<?php

namespace App\Http\Requests\Ahorros\TipoSDAT;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class MakeCondicionMontoRequest extends FormRequest
{

	use ICoreTrait;

	private $tipo = null;

	public function __construct(Route $route) {
		$this->tipo = $route->obj;
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
			'dd' => 'bail|required|numeric|min:0',
			'dh' => 'bail|required|numeric|gt:dd',
			'md' => 'bail|required|numeric|min:0',
			'mh' => 'bail|required|numeric|gt:md',
			'tasa' => 'bail|required|numeric|min:0|max:100',
		];
	}

	public function withValidator($validator) {
		if(is_null($this->dd) || is_null($this->dh) || is_null($this->md) || is_null($this->mh)) {
			return;
		}
		$validator->after(function ($validator) {
			$raw = "(? between monto_minimo and monto_maximo or ? between monto_minimo and monto_maximo)";
			$conteo = $this->tipo
				->condicionesSDAT()
				->wherePlazoMinimo($this->dd)
				->wherePlazoMaximo($this->dh)
				->whereRaw($raw, [$this->md, $this->mh])
				->count();
			if($conteo) {
				$validator->errors()->add('md', 'Rango existente.');
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
			'dd.required' => 'Los :attribute son requeridos',
			'dh.required' => 'Los :attribute son requeridos',
			'md.required' => 'El :attribute es requerido',
			'mh.required' => 'El :attribute es requerido',
			'tasa.required' => 'La :attribute es requerida',

			'dh.gt' => 'Los :attribute deben ser mayores que :value',
			'mh.gt' => 'El :attribute debe ser mayor que :value',
			'tasa.min' => 'La :attribute debe ser al menos :min',
			'tasa.max' => 'La :attribute debe ser máximo :max',
			'tasa.numeric' => 'La :attribute debe ser numérica',
		];
	}

	public function attributes() {
		return [
			'dd' => 'días desde',
			'dh' => 'días hasta',
			'md' => 'monto desde',
			'mh' => 'monto hasta',
		];
	}
}
