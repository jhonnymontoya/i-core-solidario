<?php

namespace App\Http\Requests\Creditos\TiposGarantias;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateTipoGarantiaRequest extends FormRequest
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
		$entidad = $this->getEntidad();
		return [
			//General
			'codigo'										=> [
																'bail',
																'required',
																'unique:sqlsrv.creditos.tipos_garantia,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
																'string',
																'min:1',
																'max:5'
															],
			'nombre'										=> 'bail|required|string|min:3|max:100',
			'descripcion'									=> 'bail|nullable|string|max:500',
			'tipo_garantia'									=> 'bail|required|string|in:PERSONAL,REAL,FONDOGARANTIAS',
			'condiciones'									=> 'bail|required|string',
			'monto'											=> 'bail|nullable|required_if:condiciones,requiereGarantiaPorMonto|integer|min:1',
			'valor_descubierto'								=> 'bail|nullable|required_if:condiciones,requiereGarantiaPorValorDescubierto|integer|min:1',

			//Codeudor
			'admite_codeudor_externo'						=> 'bail|required|boolean',
			'valida_cupo_codeudor'							=> 'bail|required|boolean',
			'tiene_limite_obligaciones_codeudor'			=> 'bail|required|boolean',
			'limite_obligaciones_codeudor'					=> 'bail|nullable|required_if:tiene_limite_obligaciones_codeudor,1|integer|min:1',
			'tiene_limite_saldo_codeudas'					=> 'bail|required|boolean',
			'limite_saldo_codeudas'							=> 'bail|nullable|required_if:tiene_limite_saldo_codeudas,1|integer|min:1',
			'valida_antiguedad_codeudor'					=> 'bail|required|boolean',
			'antiguedad_codeudor'							=> 'bail|nullable|required_if:valida_antiguedad_codeudor,1|integer|min:1',
			'valida_calificacion_codeudor'					=> 'bail|required|boolean',
			'calificacion_minima_requerida_codeudor'		=> 'bail|nullable|required_if:valida_calificacion_codeudor,1|string|in:A,B,C,D,E',
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
		];
	}
}
