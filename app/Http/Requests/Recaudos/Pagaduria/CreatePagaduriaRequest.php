<?php

namespace App\Http\Requests\Recaudos\Pagaduria;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreatePagaduriaRequest extends FormRequest
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
			'nombre'										=> [
																'bail',
																'required',
																'string',
																'min:3',
																'max:50',
																'unique:sqlsrv.recaudos.pagadurias,nombre,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
															],
			'periodicidad_pago'								=> 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO',
			'cuenta_por_cobrar_patronal_cuif_id'			=> [
																'bail',
																'required',
																'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
															],
			'paga_prima'									=> 'bail|required|boolean',
			'nit'											=> 'bail|required|digits:9',
			'razonSocial'									=> 'bail|required|string|min:5|max:100',
			'contacto'										=> 'bail|nullable|string|min:5|max:100',
			'contacto_email'								=> 'bail|nullable|email|string|min:5|max:100',
			'contacto_telefono'								=> 'bail|nullable|string|min:5|max:30',
			'ciudad_id'										=> [
																'bail',
																'nullable',
																'exists:sqlsrv.general.ciudades,id,deleted_at,NULL',
															],
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'cuenta_por_cobrar_patronal_cuif_id.required'	=> 'La :attribute es requerida',
			'periodicidad_pago.required'					=> 'La :attribute es requerida',
			'razonSocial.required'							=> 'La :attribute es requerida'
		];
	}

	public function attributes() {
		return [
			'cuenta_por_cobrar_patronal_cuif_id'		=> 'cuenta por cobrar patronal',
			'nit'										=> 'número de identificación tributario'
		];
	}
}
