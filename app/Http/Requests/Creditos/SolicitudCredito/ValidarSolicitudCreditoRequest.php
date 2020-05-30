<?php

namespace App\Http\Requests\Creditos\SolicitudCredito;

use App\Models\Creditos\Modalidad;
use App\Models\General\Tercero;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class ValidarSolicitudCreditoRequest extends FormRequest
{

	use ICoreTrait;

	private $mensajes = [
		'fecha_solicitud.modulocerrado' => 'Módulo de cartera cerrado para la fecha'
	];

	private $tercero = null;

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
			'fecha_solicitud'		=> 'bail|required|date_format:"d/m/Y"|modulocerrado:7',
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

		try{
			$fechaSolicitud = Carbon::createFromFormat('d/m/Y', $this->fecha_solicitud)->startOfDay();
		}
		catch(\InvalidArgumentException $e){return $reglas;}
		catch(Exception $e){return $reglas;}

		if(!empty($modalidad->minimo_antiguedad_entidad)) {
			$diferencia = $fechaSolicitud->diffInMonths($this->socioAntiguedadEntidad(), true);
			if($modalidad->minimo_antiguedad_entidad > $diferencia) {
				$this->mensajes['solicitante.regex'] = "El :attribute debe tener una antiguedad de al menos " . $modalidad->minimo_antiguedad_entidad . " meses en la entidad";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
			}
		}

		if(!empty($modalidad->minimo_antiguedad_empresa)) {
			$diferencia = $fechaSolicitud->diffInMonths($this->socioAntiguedadEmpresa(), true);

			if($modalidad->minimo_antiguedad_empresa > $diferencia) {
				$this->mensajes['solicitante.regex'] = "El :attribute debe tener una antiguedad de al menos " . $modalidad->minimo_antiguedad_empresa . " meses en la empresa";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
			}
		}

		if(!empty($modalidad->limite_obligaciones)) {
			$cantidadCreditos = $this->tercero->solicitudesCreditos()
									->whereModalidadCreditoId($modalidad->id)
									->whereEstadoSolicitud('DESEMBOLSADO')
									->count();
			if($modalidad->limite_obligaciones <= $cantidadCreditos) {
				$this->mensajes['solicitante.regex'] = "El :attribute solo puede tener " . $modalidad->limite_obligaciones . " crédito  activos por la presente modalidad";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
			}
		}

		if(!empty($modalidad->intervalo_solicitudes)) {
			$cantidadCreditos = $this->tercero->solicitudesCreditos()
									->whereModalidadCreditoId($modalidad->id)
									->whereEstadoSolicitud('DESEMBOLSADO')
									->count();
			if($cantidadCreditos) {
				$this->mensajes['solicitante.regex'] = "El :attribute ya cuenta con un crédito activo bajo la modalidad";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
			}

			$cantidadCreditos = $this->tercero->solicitudesCreditos()
									->whereModalidadCreditoId($modalidad->id)
									->whereEstadoSolicitud('SALDADO')
									->orderBy('fecha_cancelacion', 'DESC')
									->first();
			if(empty($cantidadCreditos)) {
				$diferencia = $modalidad->intervalo_solicitudes;
			}
			else {
				$diferencia = $fechaSolicitud->diffInMonths($cantidadCreditos->fecha_cancelacion, true);
			}

			if($modalidad->intervalo_solicitudes > $diferencia) {
				$this->mensajes['solicitante.regex'] = "No se cumple el intervalo de tiempo de " . $modalidad->intervalo_solicitudes . " meses entre solicitudes bajo la presente modalidad";
				array_push($reglas["solicitante"], "regex:/^$/");
				return $reglas;
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

	private function socioAntiguedadEntidad() {
		if(!$this->socioTieneEstado('ACTIVO'))return Carbon::now()->startOfDay();
		$antiguedad = $this->tercero->socio->fecha_antiguedad;
		$antiguedad = empty($antiguedad) ? Carbon::now()->startOfDay() : $antiguedad;

		return $antiguedad;
	}

	private function socioAntiguedadEmpresa() {
		if(!$this->socioTieneEstado('ACTIVO'))return false;

		$antiguedad = $this->tercero->socio->fecha_ingreso;

		return $antiguedad;
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
