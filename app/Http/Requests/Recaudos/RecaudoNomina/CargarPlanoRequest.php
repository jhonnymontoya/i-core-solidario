<?php

namespace App\Http\Requests\Recaudos\RecaudoNomina;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CargarPlanoRequest extends FormRequest
{

	use ICoreTrait;

	public function __construct(Route $route) {
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
			'archivoRecaudo'	=> 'bail|required|file|max:10240|mimetypes:text/plain',
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
