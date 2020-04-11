<?php

namespace App\Http\Requests\Ahorros\TipoSDAT;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class MakeCondicionPeriodoRequest extends FormRequest
{
	use FonadminTrait;

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
		return [
			'dd' => 'bail|required|numeric|min:0',
			'dh' => 'bail|required|numeric|gt:dd'
		];
	}

	public function withValidator($validator) {
		if(is_null($this->dd) || is_null($this->dh)) {
			return;
		}
		$validator->after(function ($validator) {
			$conteo = $this->tipo
				->condicionesSDAT()
				->where(function($query){
					$query->whereRaw("? between plazo_minimo and plazo_maximo", [$this->dd])
						->orWhereRaw("? between plazo_minimo and plazo_maximo", [$this->dh]);
				})
				->count();

			if($conteo) {
				$validator->errors()->add('dd', 'Rango existente.');
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
			'dd.required' => 'Los :attribute son requeridos',
			'dh.required' => 'Los :attribute son requeridos',

			'dh.gt' => 'Los :attribute deben ser mayores que :value'
		];
	}

	public function attributes() {
		return [
			'dd' => 'días desde',
			'dh' => 'días hasta'
		];
	}
}
