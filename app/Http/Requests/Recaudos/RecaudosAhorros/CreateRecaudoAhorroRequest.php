<?php

namespace App\Http\Requests\Recaudos\RecaudosAhorros;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateRecaudoAhorroRequest extends FormRequest
{
	use FonadminTrait;

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
			'socio'		=> [
								'bail',
								'nullable',
								'integer',
								'exists:sqlsrv.socios.socios,id,deleted_at,NULL'
						],
			'modalidad'	=> [
								'bail',
								'nullable',
								'integer',
								'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $entidad->id . ',es_reintegrable,1,deleted_at,NULL'
						],
			'fecha'		=> 'bail|nullable|date_format:"d/m/Y"'
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'data.regex'		=> $this->errorData,
			'fecha.regex'		=> 'La fecha seleccionada se encuentra en un periodo cerrado.'
		];
	}

	public function attributes() {
		return [
		];
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if(!$this->validarSocio()) {
				$validator->errors()->add('socio', 'Socio no es válido');
			}
			if(!$this->validarModalidad()) {
				$validator->errors()->add('modalidad', 'Modalidad no válido');
			}
		});
	}

	public function validarSocio() {
		$entidad = $this->getEntidad();
		if(!is_null($this->socio)) {
			$socio = Socio::find($this->socio);
			if($socio->tercero->entidad_id != $entidad->id) {
				return false;
			}
		}
		return true;
	}

	public function validarModalidad() {
		if(!is_null($this->modalidad)) {
			$modalidad = ModalidadAhorro::find($this->modalidad);
			if($modalidad->codigo == 'APO') {
				return false;
			}
		}
		return true;
	}
}
