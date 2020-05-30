<?php

namespace App\Http\Requests\General\TipoIndicador;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoIndicadorRequest extends FormRequest
{

	use ICoreTrait;

	private $route;

	public function __construct(Route $route) {
		$this->route = $route;
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
			'codigo'		=> [
								'bail',
								'required',
								'string',
								'min:2',
								'max:50',
								'unique:sqlsrv.general.tipos_indicadores,codigo,' . $this->route->obj->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
							],
			'descripcion'	=> 'bail|required|string|min:2|max:500',
			'periodicidad'	=> 'bail|required|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO',
			'variable'		=> 'bail|required|in:PORCENTAJE,VALOR',
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
		];
	}
}
