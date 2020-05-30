<?php

namespace App\Http\Requests\Ahorros\TipoSDAT;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditTipoSDATRequest extends FormRequest
{

	use ICoreTrait;

	private $tipo = null;

	public function __construct(Route $route) {
		$this->tipo = $route->obj;
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
			'codigo' => [
				'bail',
				'required',
				'string',
				'min:2',
				'max:5',
				'unique:sqlsrv.ahorros.tipos_sdat,codigo,' . $this->obj->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL'
			],
			'nombre' => 'bail|required|string|min:3|max:500',
			'esta_activo' => 'bail|required|boolean',
			'capital_cuif_id' => [
				'bail',
				'required',
				'integer',
				'min:0',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL'
			],
			'intereses_cuif_id' => [
				'bail',
				'required',
				'integer',
				'min:0',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,2,deleted_at,NULL'
			],
			'intereses_por_pagar_cuif_id' => [
				'bail',
				'required',
				'integer',
				'min:0',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL'
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
		];
	}

	public function attributes() {
		return [
			'capital_cuif_id' => 'Cuenta capital',
			'intereses_cuif_id' => 'Cuenta intereses',
			'intereses_por_pagar_cuif_id' => 'Cuenta intereses por pagar',
		];
	}
}
