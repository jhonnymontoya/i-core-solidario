<?php

namespace App\Http\Requests\Ahorros\SDAT;

use App\Models\Contabilidad\Cuif;
use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class ConstituirSDATRequest extends FormRequest
{

	use ICoreTrait;

	private $sdat = null;

	public function __construct(Route $route) {
		$this->sdat = $route->obj;

		if($this->sdat->estado != 'SOLICITUD') {
			Session::flash("error", "El SDAT debe estar en estado 'SOLICITUD' para ser constituido");
			return redirect("SDAT");
		}
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
		$exists = 'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,%s,'
			. 'tipo_cuenta,AUXILIAR,deleted_at,NULL';
		$exists = sprintf($exists, $this->getEntidad()->id);
		return [
			'cuenta' => [
				'bail',
				'required',
				'integer',
				'min:1',
				$exists
			]
		];
	}

	public function withValidator($validator) {
		if(!empty($validator->errors()->getMessages())) return null;
		$validator->after(function ($validator) {
			$cuif = Cuif::find($this->cuenta);

			if(empty($cuif)) return null;

			if($cuif->modulo_id != 1 && $cuif->modulo_id != 2) {
				$validator->errors()->add('cuenta', "La cuenta seleccionada no es válida");
			}
		});
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'cuenta.required' => 'La :attribute es requerida.',
			'cuenta.exists' => 'La :attribute seleccionada no es válida.'
		];
	}

	public function attributes() {
		return [
		];
	}
}
