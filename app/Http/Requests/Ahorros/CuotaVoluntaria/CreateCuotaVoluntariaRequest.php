<?php

namespace App\Http\Requests\Ahorros\CuotaVoluntaria;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateCuotaVoluntariaRequest extends FormRequest
{

	use FonadminTrait;

	private $socio;

	public function __construct(Route $route) {
		$this->socio = $route->obj;
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
		$repetido = false;
		if(!empty($this->modalidad_ahorro_id)) {
			foreach($this->socio->cuotasVoluntarias as $cuota) {
				if($cuota->modalidad_ahorro_id == $this->modalidad_ahorro_id) {
					$repetido = true;
					break;
				}
			}
		}
		if($repetido)return ['modalidad_ahorro_id' => 'bail|required|numeric|max:-5000'];
		$reglas = [
			'modalidad_ahorro_id'				=> [
													'bail',
													'required',
													'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $entidad->id . ',esta_activa,1,deleted_at,NULL',
												],
			'factor_calculo'					=> 'bail|required|in:PORCENTAJESUELDO,PORCENTAJESMMLV,VALORFIJO',
			'valor'								=> [
													'bail',
													'required',
													'numeric',
													'min:0.1',
												],
			'periodicidad'						=> 'bail|required|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'periodo_inicial'					=> 'bail|required|date_format:"d/m/Y"',
			'periodo_final'						=> 'bail|nullable|date_format:"d/m/Y"|after:periodo_inicial',
		];

		if($this->factor_calculo == 'PORCENTAJESUELDO') {
			array_push($reglas['valor'], 'max:100');
		}

		return $reglas;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'modalidad_ahorro_id.max'			=> 'El socio ya posee una cuota para el tipo de ahorro',
			'modalidad_ahorro_id.required'		=> 'La modalidad de ahorro es requerida',
			'valor.max'							=> 'El valor no debe superar el 100%'
		];
	}

	public function attributes() {
		return [
		];
	}
}
