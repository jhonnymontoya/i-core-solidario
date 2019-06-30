<?php

namespace App\Http\Requests\Creditos\ReliquidarCredito;

use App\Models\Creditos\SolicitudCredito;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditReliquidarCreditoRequest extends FormRequest
{
	private $solicitudDeCredito;

	public function __construct(Route $route) {
		$this->solicitudDeCredito = $route->obj;
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
		$amortizaciones = $this->solicitudDeCredito->modalidadCredito->getPeriodicidadesDePagoAdmitidas();
		$amortizaciones = implode(",", array_keys($amortizaciones));
		$reglas = [
			'freliquidar'				=> 'bail|required|integer|in:1,2', // 1 = Por plazo, 2 = Por cuota
			'fechaReliquidacion'		=> [
											'bail',
											'required',
											'date_format:"d/m/Y"',
											'modulocerrado:7'
										],

			//Validaciones por plazo
			'pplazo'					=> [
											'bail',
											'required_if:freliquidar,1',
											'integer',
											'min:1',
											'max:1000'
										],
			'pperiodicidad'				=> 'bail|required_if:freliquidar,1|string|in:' . $amortizaciones,
			'pproximoPago'				=> [
											'bail',
											'required_if:freliquidar,1',
											'date_format:"d/m/Y"'
										],

			//Validaciones por cuota
			'ccuota'					=> [
											'bail',
											'required_if:freliquidar,2',
											'integer',
											'min:1'
										],
			'cperiodicidad'				=> 'bail|required_if:freliquidar,2|string|in:' . $amortizaciones,
			'cproximoPago'				=> [
											'bail',
											'required_if:freliquidar,2',
											'date_format:"d/m/Y"'
										],
		];

		$tipoReliquidacion = empty($this->freliquidar) ? 0 : $this->freliquidar;

		if($tipoReliquidacion == 1) { //Por plazo
			array_push($reglas['fechaReliquidacion'], 'before_or_equal:pproximoPago');
		}
		else if($tipoReliquidacion == 2) { //Por cuota
			array_push($reglas['fechaReliquidacion'], 'before_or_equal:cproximoPago');
		}

		if($this->solicitudDeCredito->modalidadCredito->tipo_cuota == 'CAPITAL') {
			if($tipoReliquidacion == 1) { //Por plazo
				$reglas['pproximoPagoIntereses'] = [
													'bail',
													'required',
													'date_format:"d/m/Y"',
													'before_or_equal:pproximoPago'
												];
			}
			else if($tipoReliquidacion == 2) { //Por cuota
				$reglas['cproximoPagoIntereses']  = [
													'bail',
													'required',
													'date_format:"d/m/Y"',
													'before_or_equal:cproximoPago'
												];
			}
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
			"fechaReliquidacion.modulocerrado" => "Módulo de cartera cerrado para la fecha",
			"pplazo.max" => "El :attribute de la simulación no es coherente, se espera el número de cuotas"
		];
	}

	public function attributes() {
		return [
			'freliquidar'				=> 'forma de reliquidar',
			'pplazo'					=> 'plazo',
			'pperiodicidad'				=> 'periodicidad',
			'pproximoPago'				=> 'proximo pago',
			'ccuota'					=> 'cuota',
			'cperiodicidad'				=> 'periodicidad',
			'cproximoPago'				=> 'proximo pago',
			'pproximoPagoIntereses'		=> 'proximo pago intereses',
			'cproximoPagoIntereses'		=> 'proximo pago intereses',
		];
	}
}
