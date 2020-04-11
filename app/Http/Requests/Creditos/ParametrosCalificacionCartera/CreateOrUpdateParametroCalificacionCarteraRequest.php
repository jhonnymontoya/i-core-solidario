<?php

namespace App\Http\Requests\Creditos\ParametrosCalificacionCartera;

use App\Models\Creditos\ParametroCalificacionCartera;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateParametroCalificacionCarteraRequest extends FormRequest
{
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
			'tipoCartera'		=> 'bail|required|string|in:CONSUMO,VIVIENDA,COMERCIAL,MICROCREDITO',
			'calificacion'		=> 'bail|required|string|in:A,B,C,D,E',
			'desde'				=> 'bail|required|integer|min:0',
			'hasta'				=> 'bail|required|integer|min:0'
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
			'desde'		=> 'valor desde',
			'hasta'		=> 'valor hasta',
		];
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if(is_null($this->desde) || is_null($this->hasta))return null;
			if(!is_numeric($this->desde) || !is_numeric($this->hasta))return null;
			if($this->desde >= $this->hasta) {
				$validator->errors()->add('hasta', 'Rango de días inconsistente');
				return;
			}
			if(ParametroCalificacionCartera::contenidoEnCalificacion($this->tipoCartera, $this->calificacion, $this->desde)) {
				$validator->errors()->add('desde', 'Valor contenido en otro rango');
				return;
			}
			if(ParametroCalificacionCartera::contenidoEnCalificacion($this->tipoCartera, $this->calificacion, $this->hasta)) {
				$validator->errors()->add('hasta', 'Valor contenido en otro rango');
				return;
			}
		});
	}
}
