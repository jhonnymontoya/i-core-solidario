<?php

namespace App\Http\Requests\Socio\Socio;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditSocioInformacionLaboralRequest extends FormRequest
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
			'pagaduria_id'							=> [
														'bail',
														'required',
														'exists:sqlsrv.recaudos.pagadurias,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
													],
			'cargo'									=> 'bail|nullable|string|min:5|max:100',
			'profesion'								=> [
														'bail',
														'nullable',
														'exists:sqlsrv.general.profesiones,id,deleted_at,NULL'
													],
			'fecha_ingreso_empresa'					=> 'bail|required|date_format:"d/m/Y"|before:' . date('d/m/Y', strtotime('-1 day')),
			'tipo_contrato'							=> 'bail|required|in:INDEFINIDO,FIJO,SERVICIOS,OBRALABOR,APRENDIZ',
			'fecha_fin_contrato'					=> 'bail|nullable|required_if:tipo_contrato,FIJO,SERVICIOS,APRENDIZ|date_format:"d/m/Y"|after:tomorrow',
			'jornada_laboral'						=> 'bail|required|in:TIEMPOCOMPLETO,TIEMPOPARCIAL,TELETRABAJO',
			'codigo_nomina'							=> 'bail|nullable|string|min:3|max:45',
			'actividad_economica'					=> [
														'bail',
														'nullable',
														'exists:sqlsrv.general.ciius,id,deleted_at,NULL'
													],
			'sueldo_mensual'						=> 'bail|required|integer|min:350000',
			'valor_comision'						=> 'bail|nullable|integer',
			'periodicidad_comision'					=> 'bail|nullable|required_with:valor_comision|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'valor_prima'							=> 'bail|nullable|integer',
			'periodicidad_prima'					=> 'bail|nullable|required_with:valor_prima|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'valor_extra_prima'						=> 'bail|nullable|integer',
			'periodicidad_extra_prima'				=> 'bail|nullable|required_with:valor_extra_prima|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'valor_descuento_nomina'				=> 'bail|nullable|integer',
			'periodicidad_descuento_nomina'			=> 'bail|nullable|required_with:valor_descuento_nomina|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'valor_descuento_prima'					=> 'bail|nullable|integer',
			'periodicidad_descuento_prima'			=> 'bail|nullable|required_with:valor_descuento_prima|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL|same:periodicidad_prima',
			'valor_descuento_extra_prima'			=> 'bail|nullable|integer',
			'periodicidad_descuento_extra_prima'	=> 'bail|nullable|required_with:valor_descuento_extra_prima|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL|same:periodicidad_extra_prima',
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
			'pagaduria_id' => 'empresa'
		];
	}
}
