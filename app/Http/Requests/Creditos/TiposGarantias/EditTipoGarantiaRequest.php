<?php

namespace App\Http\Requests\Creditos\TiposGarantias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoGarantiaRequest extends FormRequest
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
			//General
			'codigo',
			'nombre',
			'descripcion',
			'tipo_garantia', //PERSONAL, REAL, FONDOGARANTIAS
			'es_permanente',
			'es_permanente_con_descubierto',
			'requiere_garantia_por_monto',
			'monto',
			'requiere_garantia_por_valor_descubierto',
			'valor_descubierto',

			//Codeudor
			'admite_codeudor_externo',
			'valida_cupo_codeudor',
			'tiene_limite_obligaciones_codeudor',
			'limite_obligaciones_codeudor',
			'tiene_limite_saldo_codeudas',
			'limite_saldo_codeudas',
			'valida_antiguedad_codeudor',
			'antiguedad_codeudor',
			'valida_calificacion_codeudor',
			'calificacion_minima_requerida_codeudor',

			'esta_activa',
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
