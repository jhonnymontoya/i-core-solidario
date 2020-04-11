<?php

namespace App\Http\Requests\Creditos\Modalidad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditModalidadCondicionesRequest extends FormRequest
{

	private $modalidad;

	public function __construct(Route $route)
	{
		$this->modalidad = $route->obj;
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$reglas =  [
			'nombre'									=> 'bail|required|string|min:5|max:50',
			'descripcion'								=> 'bail|required|string|min:10|max:1000',
			'es_exclusivo_de_socios'					=> 'bail|required|boolean',
			'esta_activa'								=> 'bail|required|boolean',
			'requiereAntiguedadEntidad'					=> 'bail|required|boolean',
			'minimo_antiguedad_entidad'					=> 'bail|nullable|required_if:requiereAntiguedadEntidad,1|integer|min:1',
			'requiereAntiguedadLaboral'					=> 'bail|required|boolean',
			'minimo_antiguedad_empresa'					=> 'bail|nullable|required_if:requiereAntiguedadLaboral,1|integer|min:1',
			'limiteObligacionesModalidad'				=> 'bail|required|boolean',
			'limite_obligaciones'						=> 'bail|nullable|required_if:limiteObligacionesModalidad,1|integer|min:1',
			'intervaloSolcitudes'						=> 'bail|required|boolean',
			'intervalo_solicitudes'						=> 'bail|nullable|required_if:intervaloSolcitudes,1|integer|min:1',
		];
		return $reglas;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'minimo_antiguedad_entidad.required_if'		=> 'El campo :attribute es requerido cuando :other sea Sí',
			'minimo_antiguedad_empresa.required_if'		=> 'El campo :attribute es requerido cuando :other sea Sí',
			'limite_obligaciones.required_if'			=> 'El campo :attribute es requerido cuando :other sea Sí',
			'intervalo_solicitudes.required_if'			=> 'El campo :attribute es requerido cuando :other sea Sí',
		];
	}

	public function attributes()
	{
		return [
		];
	}
}
