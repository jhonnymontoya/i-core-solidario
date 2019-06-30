<?php

namespace App\Http\Requests\Ahorros\TipoCuentaAhorros;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTipoCuentaAhorrosRequest extends FormRequest
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
			'nombre_producto'			=> [
											'bail',
											'required',
											'string',
											'min:5',
											'max:50',
											'unique:sqlsrv.ahorros.tipos_cuentas_ahorros,nombre_producto,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
										],
			'capital_cuif_id'			=> [
											'bail',
											'required',
											'integer',
											'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL'
										],
			'saldo_minimo'				=> 'bail|required|numeric|min:0',
			'dias_para_inactivacion'	=> 'bail|required|integer|min:0',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'capital_cuif_id.required'				=> 'La :attribute es requerida',
			'dias_para_inactivacion.required'		=> 'Los :attribute son requeridos',
		];
	}

	public function attributes() {
		return [
			'capital_cuif_id'		=> 'cuenta capital',
		];
	}
}
