<?php

namespace App\Http\Requests\Ahorros\CuentaAhorros;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditCuentaAhorrosRequest extends FormRequest
{
	use FonadminTrait;

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
		return [
			'nombre_deposito'		=> 'bail|required|string|min:5|max:50',
			'cupo_flexible'			=> 'bail|required|integer|min:0'
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
