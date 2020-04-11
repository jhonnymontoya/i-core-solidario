<?php

namespace App\Http\Requests\Tarjeta\Tarjetas;

use App\Models\Tarjeta\Tarjeta;
use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTarjetasRequest extends FormRequest
{
	use FonadminTrait;

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
			'fechaVencimiento'		=> 'bail|required|date_format:"Y/m"|after_or_equal:' . date("d/m/Y"),
			'numeroInicial'			=> 'bail|required|integer|min:0',
			'numeroFinal'			=> 'bail|required|integer|min:0|gt:numeroInicial',
		];
	}

	public function withValidator($validator) {
		if(is_null($this->numeroInicial) && empty($this->numeroInicial)) return null;
		if(is_null($this->numeroFinal) && empty($this->numeroFinal)) return null;

		$validator->after(function ($validator) {
			$cantidadTarjetas = Tarjeta::entidadId()->whereBetween("numero", [$this->numeroInicial, $this->numeroFinal])->count();

			if($cantidadTarjetas > 0) {
				$validator->errors()->add('numeroInicial', 'Se encuentran números de tarjetas registradas en el rango provisto.');
			}
			$numeroInicial = intval($this->numeroInicial);
			$esValido = Tarjeta::validarNumeroTarjeta((string)$numeroInicial);
			if(!$esValido) {
				$mensaje = sprintf("El numero de tarjeta '%s' no es válido", $numeroInicial);
				$validator->errors()->add('numeroInicial', $mensaje);
			}

			$numeroFinal = intval($this->numeroFinal);
			$esValido = Tarjeta::validarNumeroTarjeta((string)$numeroFinal);
			if(!$esValido) {
				$mensaje = sprintf("El numero de tarjeta '%s' no es válido", $numeroFinal);
				$validator->errors()->add('numeroFinal', $mensaje);
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
		];
	}

	public function attributes() {
		return [
			'fechaVencimiento'		=> 'fecha de vencimiento'
		];
	}
}
