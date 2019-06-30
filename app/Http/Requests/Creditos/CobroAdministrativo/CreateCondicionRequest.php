<?php

namespace App\Http\Requests\Creditos\CobroAdministrativo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateCondicionRequest extends FormRequest
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
	public function rules() {
		$reglas = [
			'd'		=> [
						'bail',
						'required',
						'integer',
						'min:0',
					],
			'h'		=> [
						'bail',
						'required',
						'integer',
					],
			'bc'	=> 'bail|nullable|required_if:es_condicionado,0|string|in:VALORCREDITO,VAORDESCUBIERTO',
			'fc'	=> 'bail|nullable|required_if:es_condicionado,0|string|in:VALORFIJO,PORCENTAJEBASE',
			'v'		=> [
						'bail',
						'nullable',
						'required_if:es_condicionado,0',
						'numeric',
						'min:0',
					]
		];

		//Se adiciona regla al valor, si el factor de cálculo es porcentaje,
		//el valor máximo permitido es 100
		if(! is_null($this->fc)) {
			if($this->fc == 'PORCENTAJEBASE') {
				array_push($reglas["v"], "max:100");
			}
		}

		if($this->h <= $this->d) {
			array_push($reglas["h"], "min:" . ($this->d + 1));
		}

		if($this->cobro->contenidoEnCondicion($this->d)) {
			array_push($reglas["d"], "regex:/^$/");
		}
		if($this->cobro->contenidoEnCondicion($this->h)) {
			array_push($reglas["h"], "regex:/^$/");
		}
		return $reglas;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'h.min'				=> 'El parámetro hasta, debe ser mayor al parámetro desde.',
			'd.regex'			=> 'El parámetro desde, ya se encuentra contenido en otra condición.',
			'h.regex'			=> 'El parámetro hasta, ya se encuentra contenido en otra condición.',
			'v.max'				=> 'El porcentaje no debe ser mayor a :max.'
		];
	}

	public function attributes() {
		return [
			'bc'				=> 'base de cobro',
			'fc'				=> 'factor de cálculo',
			'v'					=> 'valor'
		];
	}
}
