<?php

namespace App\Http\Requests\Creditos\AjusteCreditos;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateAjusteCreditosRequest extends FormRequest
{

	use FonadminTrait;

	private $obligacion;
	private $valorCapitalDespuesDeAjuste;

	public function __construct(Route $route) {
		$this->obligacion = $route->obj;
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
		$reglas = [
			'ajusteCapital'					=> [
												'bail',
												'nullable',
												'required_without_all:ajusteIntereses,ajusteSeguroCartera',
												'integer',
												'min:1'
											],
			'naturalezaAjusteCapital'		=> 'bail|required|string|in:AUMENTO,DECREMENTO',
			'ajusteIntereses'				=> [
												'bail',
												'nullable',
												'required_without_all:ajusteCapital,ajusteSeguroCartera',
												'integer',
												'min:1'
											],
			'naturalezaAjusteIntereses'		=> 'bail|required|string|in:AUMENTO,DECREMENTO',
			'ajusteSeguroCartera'			=> [
												'bail',
												'nullable',
												'required_without_all:ajusteCapital,ajusteIntereses',
												'integer',
												'min:1'
											],
			'naturalezaAjusteSeguro'		=> 'bail|required|string|in:AUMENTO,DECREMENTO',
			'cuifId'						=> [
												'bail',
												'nullable',
												'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,deleted_at,NULL',
											],
			'terceroContrapartidaId'		=> [
												'bail',
												'required',
												'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
											],
			'referencia'					=> 'bail|nullable|string|max:100',
			'comentarios'					=> 'bail|required|string|min:3|max:1000',
		];

		$ajuste = 0;

		if(!empty($this->ajusteCapital)) {
			$ajuste += $this->naturalezaAjusteCapital == 'AUMENTO' ? $this->ajusteCapital : -$this->ajusteCapital;
			$this->valorCapitalDespuesDeAjuste = $this->obligacion->saldoObligacion('31/12/2100') + $ajuste;
			if($this->valorCapitalDespuesDeAjuste < 0) {
				array_push($reglas['ajusteCapital'], 'regex:/^$/');
			}
		}

		if(!empty($this->ajusteIntereses)) {
			$ajuste += $this->naturalezaAjusteIntereses == 'AUMENTO' ? $this->ajusteIntereses : -$this->ajusteIntereses;
		}

		if(!empty($this->ajusteSeguroCartera)) {
			$ajuste += $this->naturalezaAjusteSeguro == 'AUMENTO' ? $this->ajusteSeguroCartera : -$this->ajusteSeguroCartera;
		}

		if($ajuste != 0) {
			array_push($reglas['cuifId'], 'required');
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
			'ajusteCapital.required_without_all'		=> 'Se debe ingresar un valor en al menos un concepto de ajuste',
			'ajusteIntereses.required_without_all'		=> 'Se debe ingresar un valor en al menos un concepto de ajuste',
			'ajusteSeguroCartera.required_without_all'	=> 'Se debe ingresar un valor en al menos un concepto de ajuste',
			'cuifId.required'							=> 'La :attribute es requerida',
			'ajusteCapital.regex'						=> 'Con el ajuste, el capital queda con un saldo negativo de $' . number_format($this->valorCapitalDespuesDeAjuste, 0),
		];
	}

	public function attributes() {
		return [
			'cuifId'		=> 'cuenta contrapartida',
			'comentarios'	=> 'detalle',
		];
	}
}
