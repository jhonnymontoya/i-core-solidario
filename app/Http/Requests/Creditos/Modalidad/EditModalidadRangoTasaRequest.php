<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadRangoTasaRequest extends FormRequest
{
	private $modalidad;
	private $condicion;

	public function __construct(Route $route) {
		$this->modalidad = $route->obj;

		$this->condicion = $this->modalidad->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
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
			'condicionadoDesde'				=> [
												'bail',
												'required',
												'integer',
												'min:0',
											],
			'condicionadoHasta'				=> [
												'bail',
												'required',
												'integer',
												'min:0',
											],
			'tipoCondicionTasa'				=> 'bail|required|numeric|min:0.1|max:100',
		];

		if($this->condicion->contenidoEnCondicion($this->condicionadoDesde)) {
			array_push($reglas['condicionadoDesde'], 'regex:/^$/');
		}
		if($this->condicion->contenidoEnCondicion($this->condicionadoHasta)) {
			array_push($reglas['condicionadoHasta'], 'regex:/^$/');
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
			'condicionadoDesde.regex'			=> 'El rango ingresado contine valores contenidos en otro rango',
			'condicionadoHasta.regex'			=> 'El rango ingresado contine valores contenidos en otro rango',
		];
	}

	public function attributes() {
		return [
		];
	}
}
