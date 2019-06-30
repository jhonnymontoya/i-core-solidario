<?php

namespace App\Http\Requests\Socio\TipoCuotaObligatoria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoCuotaObligatoriaRequest extends FormRequest
{

	private $route;

	public function __construct(Route $route) {
		$this->route = $route;
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
			'tipo_calculo'		=> 'bail|required|in:PORCENTAJESUELDO,PORCENTAJESMMLV,VALORFIJO',
			//'valor'				=> 'bail|required|numeric|min:0.01',
			'tope'				=> 'bail|nullable|numeric|min:0.01',
			'esta_activa'		=> 'bail|required|boolean',
		];

		switch ($this->tipo_calculo) {
			case 'PORCENTAJESUELDO':
				$reglas['valor'] = 'bail|required|numeric|min:0.01|max:100';
				break;			
			default:
				$reglas['valor'] = 'bail|required|numeric|min:0.01';
				break;
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
			'valor.max'			=> 'El :attribute no puede ser mayor a :max cuando el tipo de cálculo es % sueldo',
		];
	}

	public function attributes() {
		return [
		];
	}
}
