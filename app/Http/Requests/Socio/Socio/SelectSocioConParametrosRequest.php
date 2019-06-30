<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class SelectSocioConParametrosRequest extends FormRequest
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
			'id'								=> 'nullable|integer|min:1',

			'pagaduria'							=> 'nullable|string|min:5|max:100',

			'fechaAntiguedadEmpresaIgualA'		=> 'nullable|date_format:"d/m/Y"',
			'fechaAntiguedadEmpresaMenorA'		=> 'nullable|date_format:"d/m/Y"',
			'fechaAntiguedadEmpresaMayorA'		=> 'nullable|date_format:"d/m/Y"',

			'fechaAntiguedadFondoIgualA'		=> 'nullable|date_format:"d/m/Y"',
			'fechaAntiguedadFondoMenorA'		=> 'nullable|date_format:"d/m/Y"',
			'fechaAntiguedadFondoMayorA'		=> 'nullable|date_format:"d/m/Y"',

			'tipoContrato'						=> 'nullable|in:INDEFINIDO,FIJO,SERVICIOS,OBRALABOR,APRENDIZ',

			'sueldoIgualA'						=> 'nullable|integer|min:1',
			'sueldoMenorA'						=> 'nullable|integer|min:1',
			'sueldoMayorA'						=> 'nullable|integer|min:1',

			'estadoIgualA'						=> 'nullable|in:PROCESO,LIQUIDADO,RETIRO,NOVEDAD,ACTIVO',
			'estadoDiferenteA'					=> 'nullable|in:PROCESO,LIQUIDADO,RETIRO,NOVEDAD,ACTIVO',
		];
	}
}
