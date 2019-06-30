<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadTasaRequest extends FormRequest
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
		$reglas =  [
			'nombre'					=> 'bail|required|string|min:5|max:50',
			'descripcion'				=> 'bail|required|string|min:10|max:1000',
			'es_exclusivo_de_socios'	=> 'bail|required|boolean',
			'tipo_tasa'					=> [
											'bail',
											'required',
											'string',
											'in:FIJA,VARIABLE,SINTASA',
										],
			'pago_interes'				=> 'bail|nullable|required_if:tipo_tasa,FIJA,VARIABLE|string|in:VENCIDOS,ANTICIPADOS',
			'aplica_mora'				=> 'bail|required|boolean',
			'tasa_mora'					=> 'bail|nullable|required_if:aplica_mora,1|numeric|min:0.1|max:100',
			'tasa_condicionada'			=> [
											'bail',
											'nullable',
											'required_if:tipo_tasa,FIJA,VARIABLE',
											'boolean',
										],
			'tasa'						=> [
											'bail',
											'nullable',
											'numeric',
											'max:100',
										],
			'condicionPor'				=> [
											'bail',
											'nullable',
											'required_if:tasa_condicionada,1',
											'in:ANTIGUEDADEMPRESA,ANTIGUEDADENTIDAD,MONTO,PLAZO',
										],
			'esta_activa'				=> 'bail|required|boolean',
		];
		if(!$this->tasa_condicionada && $this->condicion != null) {
			if($this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['tasa_condicionada'], 'regex:/^$/');
			}
		}
		if($this->tasa_condicionada && $this->condicion != null) {
			if($this->condicion->condicionado_por != $this->condicionPor && $this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['condicionPor'], 'regex:/^$/');
			}
		}
		if($this->tipo_tasa == 'SINTASA' && $this->condicion != null) {
			if($this->condicion->rangosCondicionesModalidad->count()) {
				array_push($reglas['tipo_tasa'], 'regex:/^$/');
			}
		}
		if($this->tipo_tasa != 'SINTASA') {
			array_push($reglas['tasa'], 'required_if:tasa_condicionada,0');
		}
		if($this->tasa_condicionada || $this->tipo_tasa == 'SINTASA') {
			array_push($reglas['tasa'], 'min:0');
		}
		else {
			array_push($reglas['tasa'], 'min:0.01');
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
			'tipo_tasa.regex'			=> 'Imposible cambiar el tipo de tasa, existen rangos asociados a la condición',
			'tasa_mora.required_if'		=> 'La tasa de mora es requerida',
			'tasa.max'					=> 'La tasa supera el parámetro máximo',
			'tasa.min'					=> 'La :attribute debe ser de al menos :min',
			'tasa.required_if'			=> 'Tasa querida',
			'condicionPor.required_if'	=> 'La condición es requerida',
			'tasa_condicionada.regex'	=> 'Imposible cambiar el método de tasa, existen rangos asociados a la condición',
			'condicionPor.regex'		=> 'No se puede cambiar la condición, existen rangos asociados',
		];
	}

	public function attributes() {
		return [
		];
	}
}
