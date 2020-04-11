<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadRequest extends FormRequest
{

	private $modalidad;
	private $condicion;

	public function __construct(Route $route) {
		$this->modalidad = $route->obj;

		$this->condicion = $this->modalidad->condicionesModalidad->where('tipo_condicion', 'PLAZO')->first();
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
		$reglas =  [
			'nombre'					=> 'bail|required|string|min:5|max:50',
			'descripcion'				=> 'bail|required|string|min:10|max:1000',
			'plazo_condicionado'		=> [
											'bail',
											'required',
											'boolean',
										],
			'plazo'						=> 'bail|nullable|required_if:plazo_condicionado,0|integer|min:1|max:600',
			'condicionPor'				=> [
											'bail',
											'nullable',
											'required_if:plazo_condicionado,1',
											'in:ANTIGUEDADEMPRESA,ANTIGUEDADENTIDAD,MONTO',
										],
			'es_exclusivo_de_socios'	=> 'bail|required|boolean',
			'esta_activa'				=> 'bail|required|boolean',
		];

		if(!$this->plazo_condicionado && $this->condicion != null) {
			if($this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['plazo_condicionado'], 'regex:/^$/');
			}
		}
		if($this->plazo_condicionado && $this->condicion != null) {
			if($this->condicion->condicionado_por != $this->condicionPor && $this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['condicionPor'], 'regex:/^$/');
			}
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
			'plazo.required_if'			=> 'El plazo máximo en meses es requerido',
			'plazo.max'					=> 'El plazo supera el parámetro máximo',
			'condicionPor.required_if'	=> 'La condición es requerida',
			'plazo_condicionado.regex'	=> 'Imposible cambiar el método de plazo, existen rangos asociados a la condición',
			'condicionPor.regex'		=> 'No se puede cambiar la condición, existen rangos asociados',
		];
	}

	public function attributes() {
		return [
		];
	}
}
