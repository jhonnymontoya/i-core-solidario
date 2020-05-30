<?php

namespace App\Http\Requests\Socio\TipoCuotaObligatoria;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTipoCuotaObligatoriaRequest extends FormRequest
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
		$reglas = [
			'codigo'			=> [
									'bail',
									'required',
									'string',
									'min:2',
									'max:5',
									'unique:sqlsrv.ahorros.modalidades_ahorros,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL'
								],
			'nombre'			=> 'bail|required|string|min:2|max:100',
			'cuif_id'			=> [
									'bail',
									'required',
									'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL',
								],
			'es_reintegrable'	=> 'bail|required|boolean',
			'tipo_calculo'		=> 'bail|required|in:PORCENTAJESUELDO,PORCENTAJESMMLV,VALORFIJO',
			//'valor'				=> 'bail|required|numeric|min:0.01',
			'tope'				=> 'bail|nullable|numeric|min:0.01',
		];

		switch ($this->tipo_calculo) {
			case 'PORCENTAJESUELDO':
				$reglas['valor'] = 'bail|required|numeric|min:0.01|max:100';
				break;			
			default:
				$reglas['valor'] = 'bail|required|numeric|min:0.01';
				break;
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
			'cuif_id.exists'	=> 'La :attribute seleccionada es inválida.',
			'valor.max'			=> 'El :attribute no puede ser mayor a :max cuando el tipo de cálculo es % sueldo'
		];
	}

	public function attributes() {
		return [
			'cuif_id'	=> 'cuenta',
		];
	}
}
