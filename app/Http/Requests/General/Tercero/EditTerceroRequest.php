<?php

namespace App\Http\Requests\General\Tercero;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTerceroRequest extends FormRequest
{

	use FonadminTrait;

	private $tercero;

	public function __construct(Route $route) {
		$this->tercero = $route->obj;
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
		if($this->tercero->tipo_tercero == 'NATURAL') {
			$reglas = [
				'esta_activo'							=> 'bail|required|boolean',
				'tipo_identificacion_id'				=> [
															'bail',
															'required',
															'integer',
															'exists:sqlsrv.general.tipos_identificacion,id,aplicacion,NATURAL,esta_activo,1,deleted_at,NULL'
														],
				'numero_identificacion' 				=> [
															'bail',
															'required',
															'digits_between:4,15',
															'unique:sqlsrv.general.terceros,numero_identificacion,' . $this->tercero->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
														],
				'primer_nombre'							=> 'bail|required|string|min:2|max:100',
				'segundo_nombre'						=> 'bail|nullable|string|min:2|max:100',
				'primer_apellido'						=> 'bail|required|string|min:2|max:100',
				'segundo_apellido'						=> 'bail|nullable|string|min:2|max:100',
				'sexo_id'								=> [
															'bail',
															'required',
															'exists:sqlsrv.general.sexos,id,deleted_at,NULL'
														],
				'fecha_nacimiento'						=> 'bail|nullable|date_format:"d/m/Y"',
				'ciudad_nacimiento_id'					=> [
															'bail',
															'nullable',
															'integer',
															'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
														],
				'fecha_expedicion_documento_identidad'	=> 'bail|nullable|date_format:"d/m/Y"',
				'ciudad_expedicion_documento_id'		=> [
															'bail',
															'nullable',
															'integer',
															'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
														],
				'actividad_economica_id'				=> [
															'bail',
															'nullable',
															'integer',
															'exists:sqlsrv.general.ciius,id,deleted_at,NULL'
														],

				'ciudad_id'								=> [
															'bail',
															'nullable',
															'required_with:direccion',
															'integer',
															'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
														],
				'direccion'								=> [
															'bail',
															'nullable',
															'string',
															'min:5',
															'max:255',
														],
				'telefono'								=> 'bail|nullable|string|min:4|max:20',
				'extension'								=> 'bail|nullable|string|min:1|max:8',
				'movil'									=> 'bail|nullable|string|min:10|max:20',
				'email'									=> 'bail|nullable|email',
			];
		}
		else {
			$reglas = [
				'esta_activo'				=> 'bail|required|boolean',
				'tipo_identificacion_id'	=> [
												'bail',
												'required',
												'integer',
												'exists:sqlsrv.general.tipos_identificacion,id,aplicacion,JURIDICA,esta_activo,1,deleted_at,NULL'
											],
				'numero_identificacion' 	=> [
												'bail',
												'required',
												'digits_between:4,15',
												'unique:sqlsrv.general.terceros,numero_identificacion,' . $this->tercero->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
											],
				'razon_social'				=> 'bail|required|string|min:2|max:100',
				'sigla'						=> 'bail|nullable|string|min:2|max:50',
				'fecha_constitucion'		=> 'bail|nullable|date_format:"d/m/Y"',
				'ciudad_constitucion_id'	=> [
												'bail',
												'nullable',
												'integer',
												'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
											],
				'numero_matricula'			=> 'bail|nullable|string|min:2|max:15',
				'matricula_renovada'		=> 'bail|nullable|boolean',
				'actividad_economica_id'	=> [
												'bail',
												'nullable',
												'integer',
												'exists:sqlsrv.general.ciius,id,deleted_at,NULL'
											],

				'ciudad_id'					=> [
												'bail',
												'nullable',
												'required_with:direccion',
												'integer',
												'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
											],
				'direccion'					=> [
												'bail',
												'nullable',
												'string',
												'min:5',
												'max:255',
											],
				'telefono'					=> 'bail|nullable|string|min:4|max:20',
				'extension'					=> 'bail|nullable|string|min:1|max:8',
				'movil'						=> 'bail|nullable|string|min:10|max:20',
				'email'						=> 'bail|nullable|email',
			];
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
			'direccion.min'			=> 'La :attribute debe tener al menos :min caracteres.',
			'direccion.max'			=> 'La :attribute no debe tener más de :max caracteres.',
			'extension.max'			=> 'La :attribute no debe tener más de :max caracteres.',
			'segundo_nombre.min'	=> ':attribute debe tener al menos :min caracteres.',
			'segundo_nombre.max'	=> ':attribute no debe tener más de :max caracteres.',
		];
	}

	public function attributes() {
		return [
			'tipo_identificacion_id'	=> 'tipo de identificación',
			'segundo_nombre'			=> 'Otros nombre',
			'sexo_id'					=> 'sexo',
			'ciudad_id'					=> 'ciudad',
			'extension'					=> 'extensión',
			'movil'						=> 'celular',
		];
	}
}
