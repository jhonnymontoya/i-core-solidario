<?php

namespace App\Http\Requests\General\Entidad;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\General\TipoIdentificacion;

class CreateEntidadRequest extends FormRequest
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
		$nit = TipoIdentificacion::activo(true)
				->where('aplicacion', 'JURÍDICA')
				->where('codigo', 'NIT')
				->first();

		if($nit == null)abort(404, 'No se encuentra el tipo de identificación \'NIT\' para el tipo de persona \'JURÍDICA\'');

		return [
			'razon'						=> 'bail|required|string|min:5|max:100',
			'sigla'						=> 'bail|nullable|string|min:2|max:50',
			'nit'						=> [
											'bail',
											'required',
											'digits:9'
										],
			'actividad_economica'		=> [
											'bail',
											'required',
											'exists:sqlsrv.general.ciius,id,deleted_at,NULL'
										],
			'fecha_inicio_contabilidad'	=> 'bail|required|date_format:"d/m/Y"|before:' . date('Y-m-d', strtotime('+6 month')),
			'usa_dependencia'			=> 'bail|required|boolean',
			'usa_centro_costos'			=> 'bail|required|boolean',
			'pagina_web'				=> 'bail|nullable|url',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'razon.min'								=> 'La :attribute debe tener al menos :min caracteres.',
			'razon.max'								=> 'La :attribute no debe tener más de :max caracteres.',
			'fecha_inicio_contabilidad.before'		=> 'La fecha está demasiado adelantada.',
		];
	}

	public function attributes() {
		return [
			'razon'		=> 'razón social',
			'nit'		=> 'número de identificación tributária',
		];
	}
}
