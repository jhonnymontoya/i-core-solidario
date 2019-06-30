<?php

namespace App\Http\Requests\Contabilidad\Impuesto;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditImpuestoRequest extends FormRequest
{
	use FonadminTrait;

	private $impuesto;

	public function __construct(Route $route) {
		$this->impuesto = $route->obj;
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
		$nombreUnico = 'unique:sqlsrv.contabilidad.impuestos,nombre,%s,id,entidad_id,%s,deleted_at,NULL';
		$nombreUnico = sprintf($nombreUnico, $this->impuesto->id, $entidad->id);
		return [
			'nombre' => [
				'bail',
				'required',
				'string',
				'min:3',
				'max:250',
				$nombreUnico
			],
			"tipo" => "bail|required|string|in:NACIONAL,DISTRITAL,REGIONAL",
			"esta_activo" => "bail|required|boolean",
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
