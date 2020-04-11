<?php

namespace App\Http\Requests\Creditos\SolicitudCredito;

use App\Models\Creditos\Modalidad;
use App\Models\General\Tercero;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class DesembolsarSolicitudCreditoRequest extends FormRequest
{

	use FonadminTrait;

	private $mensajes = [
		'fecha_primer_pago_intereses.before_or_equal' => 'El primer pago de intereses no puede ser posterior al primer pago de capital',
		'fecha_primer_pago_intereses.same'	=> 'A una cuota la fecha de primer pago y la fecha primer pago intereses deben ser iguales',
		'fecha_aprobacion.modulocerrado' => 'Módulo de cartera cerrado para la fecha',
		'fecha_desembolso.modulocerrado' => 'Módulo de cartera o contable cerrado para la fecha',
		'plazo.max'	=> 'El :attribute de la simulación no es coherente, se espera el número de cuotas'
	];

	private $tercero = null;

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
		$entidad = $this->getEntidad();
		$reglas = [
			'modalidad'				=> [
										'bail',
										'required',
										'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $entidad->id . ',esta_activa,1,uso_para_tarjeta,0,deleted_at,NULL'
									],
			'solicitante'			=> [
										'bail',
										'required',
										'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1',
									],
			'fecha_aprobacion'		=> 'bail|required|date_format:"d/m/Y"',
			'fecha_desembolso'		=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7|modulocerrado:2',

			'valor_credito'			=> 'bail|required|numeric|min:1',
			'plazo'					=> 'bail|required|integer|min:1|max:1000',
			'forma_pago'			=> 'bail|required|string|in:NOMINA,PRIMA,CAJA',
			'periodicidad'			=> 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL',
			'fecha_primer_pago'		=> 'bail|required|date_format:"d/m/Y"',
			'observaciones'			=> 'bail|nullable|string|max:1000',
		];

		$modalidad = Modalidad::find($this->modalidad);
		if($modalidad == null) {
			return $reglas;
		}

		$this->tercero = Tercero::find($this->solicitante);
		if($this->tercero == null) {
			return $reglas;
		}

		if($modalidad->es_exclusivo_de_socios) {
			if(!$this->socioTieneEstado('ACTIVO')) {
				$this->mensajes['solicitante.regex'] = "El :attribute debe ser un socio activo";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
			}
		}

		if($this->solicitud->modalidadCredito->tipo_cuota == 'CAPITAL') {
			$reglas['fecha_primer_pago_intereses'] = ['bail', 'required', 'date_format:"d/m/Y"', 'before_or_equal:fecha_primer_pago'];
			if($this->plazo == 1) {
				array_push($reglas['fecha_primer_pago_intereses'], 'same:fecha_primer_pago');
			}
		}

		return $reglas;
	}

	private function terceroEsAsociado() {
		if(empty($this->tercero))return false;
		return $this->tercero->es_asociado;
	}

	private function socioTieneEstado($estado) {
		if(!$this->terceroEsAsociado())return false;

		if($this->tercero->socio == null) {
			return null;
		}

		return $this->tercero->socio->estado == $estado;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return $this->mensajes;
	}

	public function attributes() {
		return [
		];
	}
}
