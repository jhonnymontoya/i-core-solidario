<?php

namespace App\Http\Requests\Ahorros\TipoCuentaAhorros;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoCuentaAhorrosRequest extends FormRequest
{
	use ICoreTrait;

	private $obj;

	public function __construct(Route $route) {
		$this->obj = $route->obj;
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
		$entidad = $this->getEntidad();
		return [
			'nombre_producto'			=> [
											'bail',
											'required',
											'string',
											'min:5',
											'max:50',
											'unique:sqlsrv.ahorros.tipos_cuentas_ahorros,nombre_producto,' . $this->obj->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
										],
			'capital_cuif_id'			=> [
											'bail',
											'required',
											'integer',
											'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL'
										],
			'saldo_minimo'				=> 'bail|required|numeric|min:0',
			'dias_para_inactivacion'	=> 'bail|required|integer|min:0',
			'esta_activa'				=> 'bail|required|boolean'
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
