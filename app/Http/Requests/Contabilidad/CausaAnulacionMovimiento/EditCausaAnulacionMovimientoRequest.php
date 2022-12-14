<?php

namespace App\Http\Requests\Contabilidad\CausaAnulacionMovimiento;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditCausaAnulacionMovimientoRequest extends FormRequest
{

	use ICoreTrait;

	private $obj;
	public function __construct(Route $route) {
		$this->obj = $route->obj;
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
			'nombre'			=> [
									'bail',
									'required',
									'string',
									'min:5',
									'max:45',
									'unique:sqlsrv.contabilidad.causas_anulacion_movimiento,nombre,' . $this->obj->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'esta_activa'		=> 'bail|required|boolean',
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
			'esta_activa'		=> 'estado',
		];
	}
}
