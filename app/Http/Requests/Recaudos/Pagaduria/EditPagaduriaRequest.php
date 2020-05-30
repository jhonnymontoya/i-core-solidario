<?php

namespace App\Http\Requests\Recaudos\Pagaduria;

use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditPagaduriaRequest extends FormRequest
{

	use ICoreTrait;

	private $obj;
	private $anioProcesado = false;

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
		$entidad = $this->getEntidad();
		$reglas = [
			'nombre'										=> [
																'bail',
																'required',
																'string',
																'min:3',
																'max:50',
																'unique:sqlsrv.recaudos.pagadurias,nombre,' . $this->obj->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
															],
			'periodicidad_pago'								=> 'bail|required|string|in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO',
			'cuenta_por_cobrar_patronal_cuif_id'			=> [
																'bail',
																'required',
																'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
															],
			'paga_prima'									=> 'bail|required|boolean',
			'nit'											=> 'bail|required|digits:9',
			'razonSocial'									=> 'bail|required|string|min:5|max:100',
			'contacto'										=> 'bail|nullable|string|min:5|max:100',
			'contacto_email'								=> 'bail|nullable|email|string|min:5|max:100',
			'contacto_telefono'								=> 'bail|nullable|string|min:5|max:30',
			'ciudad_id'										=> [
																'bail',
																'nullable',
																'exists:sqlsrv.general.ciudades,id,deleted_at,NULL',
															],
			'fecha_inicio_recaudo'							=> 'bail|required|date_format:"d/m/Y"',
			'fecha_inicio_reporte'							=> 'bail|required|date_format:"d/m/Y"|before_or_equal:fecha_inicio_recaudo',
			'anioPeriodo'									=> [
																'bail',
																'nullable',
																'required_if:programar,true',
																'digits:4'
															],
		];

		if(!$this->obj->calendarioRecaudos->count()) {
			return $reglas;
		}
		if(!empty($this->programar)) {
			if(!empty($this->anioPeriodo)) {
				//Se valida que el año no esté procesado
				$inicio = new Carbon('first day of January ' . $this->anioPeriodo, config('app.timezone'));
				$fin = new Carbon('last day of December ' . $this->anioPeriodo, config('app.timezone'));
				if($this->obj->calendarioRecaudos()->whereBetween('fecha_recaudo', [$inicio, $fin])->count()) {
					array_push($reglas['anioPeriodo'], 'regex:/^$/');
					$this->anioProcesado = true;
					return $reglas;
				}

				//Se valida que el año anterior este procesado
				$inicio = new Carbon('first day of January ' . ($this->anioPeriodo - 1), config('app.timezone'));
				$fin = new Carbon('last day of December ' . ($this->anioPeriodo - 1), config('app.timezone'));
				if(!$this->obj->calendarioRecaudos()->whereBetween('fecha_recaudo', [$inicio, $fin])->count()) {
					array_push($reglas['anioPeriodo'], 'regex:/^$/');
					$this->anioProcesado = false;
					return $reglas;
				}
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
			'cuenta_por_cobrar_patronal_cuif_id.required'	=> 'La :attribute es requerida',
			'periodicidad_pago.required'					=> 'La :attribute es requerida',
			'razonSocial.required'							=> 'La :attribute es requerida',
			'fecha_inicio_recaudo.required'					=> 'La :attribute es requerida',
			'fecha_inicio_reporte.required'					=> 'La :attribute es requerida',
			'anioPeriodo.required_if'						=> 'El :attribute es requerido para programar',
			'anioPeriodo.regex'								=> $this->anioProcesado ? 'El periodo a programar ya se encuentra programado' : 'Falta programación periodos anteriores',
		];
	}

	public function attributes() {
		return [
			'cuenta_por_cobrar_patronal_cuif_id'		=> 'cuenta por cobrar patronal',
			'nit'										=> 'número de identificación tributario',
			'anioPeriodo'								=> 'año programación periodo'
		];
	}
}
