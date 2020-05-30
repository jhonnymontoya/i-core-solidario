<?php

namespace App\Http\Requests\Creditos\SolicitudCredito;

use App\Traits\ICoreTrait;
use Illuminate\Routing\Route;
use Illuminate\Foundation\Http\FormRequest;

class MakeCuotaExtraordinariaRequest extends FormRequest
{
	use ICoreTrait;

	private $solicitud;

	public function __construct(Route $route) {
		$this->solicitud = $route->obj;
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
			'numero_cuotas' => 'bail|required|integer|min:1',
			'valor_cuota' => 'bail|required|integer|min:1',
			'forma_pago' => 'bail|required|string|in:NOMINA,CAJA,PRIMA',
			'periodicidad' => 'bail|required|string|in:' . $this->listaPeriodicidades(),
			'inicio_descuento' => 'bail|required|string|date_format:"d/m/Y"|in:' . $this->listaProgramaciones(),
		];
	}

	private function listaProgramaciones() {
		$socio = $this->solicitud->tercero->socio;
		$listaProgramaciones = array();
		if($socio) {
			$programaciones = $socio->pagaduria->calendarioRecaudos()->whereEstado('PROGRAMADO')->get();
			foreach($programaciones as $programacion) {
				$listaProgramaciones[] = $programacion->fecha_recaudo->format('d/m/Y');
			}
		}
		return implode(",", $listaProgramaciones);
	}

	private function listaPeriodicidades() {
		$periodicidades = array(
			'SEMANAL',
			'DECADAL',
			'CATORCENAL',
			'QUINCENAL',
			'MENSUAL',
			'BIMESTRAL',
			'TRIMESTRAL',
			'CUATRIMESTRAL',
			'SEMESTRAL',
			'ANUAL'
		);
		return implode(",", $periodicidades);
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
