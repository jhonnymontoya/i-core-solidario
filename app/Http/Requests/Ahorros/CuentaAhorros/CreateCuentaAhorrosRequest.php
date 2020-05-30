<?php

namespace App\Http\Requests\Ahorros\CuentaAhorros;

use App\Models\Socios\Socio;
use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateCuentaAhorrosRequest extends FormRequest
{
	use ICoreTrait;

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
			'tipo_cuenta_ahorro_id' => [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.ahorros.tipos_cuentas_ahorros,id,entidad_id,' . $entidad->id . ',esta_activa,1,deleted_at,NULL',
									],
			'titular_socio_id'		=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.socios.socios,id,estado,ACTIVO,deleted_at,NULL'
									]
		];
	}

	public function withValidator($validator) {
		if(!(empty($this->titular_socio_id) || is_null($this->titular_socio_id))) {
			if(intval($this->titular_socio_id) > 0) {
				$validator->after(function ($validator) {
					$socio = Socio::find($this->titular_socio_id);
					if($socio != null) {
						if($socio->tercero->entidad_id != $this->getEntidad()->id) {
							$validator->errors()->add('titular_socio_id', 'El socio seleccionado no es válido.');
						}
					}
				});
			}
		}
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
			'tipo_cuenta_ahorro_id'		=> 'tipo cuenta ahorro'
		];
	}
}
