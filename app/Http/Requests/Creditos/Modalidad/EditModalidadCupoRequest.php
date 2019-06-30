<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadCupoRequest extends FormRequest
{
	private $modalidad;
	private $condicion;

	public function __construct(Route $route)
	{
		$this->modalidad = $route->obj;

		$this->condicion = $this->modalidad->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();
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
			'es_exclusivo_de_socios'	=> 'bail|required|boolean',
			'esta_activa'				=> 'bail|required|boolean',
			'afecta_cupo'				=> 'bail|required|boolean',
			'es_monto_cupo'				=> 'bail|required|boolean',
			'es_monto_condicionado'		=> [
											'bail',
											'required',
											'boolean',
										],
			'monto'						=> [
											'bail',
											'nullable',
											'integer',
										],
			'condicionPor'				=> [
											'bail',
											'nullable',
											'required_if:es_monto_condicionado,1',
											'in:ANTIGUEDADEMPRESA,ANTIGUEDADENTIDAD,PLAZO',
										],
		];

		if(!$this->es_monto_condicionado && $this->condicion != null) {
			if($this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['es_monto_condicionado'], 'regex:/^$/');
			}
		}
		if($this->es_monto_condicionado && $this->condicion != null) {
			if($this->condicion->condicionado_por != $this->condicionPor && $this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['condicionPor'], 'regex:/^$/');
			}
		}
		if($this->es_monto_condicionado == '0') {
			if($this->es_monto_cupo == '0') {
				array_push($reglas['monto'], 'required_if:es_monto_condicionado,0');
				array_push($reglas['monto'], 'min:1');
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
			'monto.required_if'				=> 'El monto máximo es requerido',
			'condicionPor.required_if'		=> 'La condición es requerida',
			'es_monto_condicionado.regex'	=> 'Imposible cambiar el método de monto, existen rangos asociados a la condición',
			'condicionPor.regex'			=> 'No se puede cambiar la condición, existen rangos asociados',
		];
	}

	public function attributes() {
		return [
		];
	}
}
