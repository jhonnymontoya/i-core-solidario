<?php

namespace App\Http\Requests\Recaudos\RecaudosCaja;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Creditos\SolicitudCredito;
use Validator;
use DB;

class MakeRecaudoCajaRequest extends FormRequest
{
	use ICoreTrait;

	private $errorData = "Error en la data";
	private $entidad = null;

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
		$this->entidad = $this->getEntidad();
		return [
			'tercero'				=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.general.terceros,id,entidad_id,' . $this->entidad->id . ',tipo_tercero,NATURAL,esta_activo,1,deleted_at,NULL'
									],
			'cuenta'				=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $this->entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,1,deleted_at,NULL'
									],
			'fecha'					=> [
										'bail',
										'required',
										'date_format:"d/m/Y"',
									],
			'data'					=> [
										'bail',
										'required',
										'json'
									]
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

	public function withValidator($validator) {
		if(!(empty($this->tercero) || is_null($this->tercero))) {
			if(intval($this->tercero) > 0) {
				$validator->after(function ($validator) {
					//Valida si los modulos estan cerrados
					if($this->modulosCerradosParaFecha()) {
						$validator->errors()->add('fecha', 'La fecha seleccionada se encuentra en un periodo cerrado.');
					}
					//se valida que los datos existan y que al menos existan un abono
					if(!$this->tieneAjustes()) {
						$validator->errors()->add('data', $this->errorData);
					}
					//validar Ahorros
					if(!$this->ahorrosValidos()) {
						$validator->errors()->add('data', $this->errorData);
					}
					//validar Ahorros
					if(!$this->creditosValidos()) {
						$validator->errors()->add('data', $this->errorData);
					}
				});
			}
		}
	}

	/**
	 * Se valida si los módulos de [ahorros, contabilidad, cartera] se encuentran cerrados para la fecha
	 * @return type
	 */
	public function modulosCerradosParaFecha() {
		//Se valida el módulo de contabilidad
		if($this->moduloCerrado(2, $this->fecha)) return true;
		//Se valida el módulo de ahorros
		if($this->moduloCerrado(6, $this->fecha)) return true;
		//Se valida el módulo de cartera
		if($this->moduloCerrado(7, $this->fecha)) return true;
		//De lo contrario, esta bien
		return false;
	}

	/**
	 * Valida si tiene ajustes para procesar
	 * @return type
	 */
	public function tieneAjustes() {
		$data = json_decode($this->data);
		//Se valida si los datos se encuentran bien formados
		if(is_null($data->ahorros) || is_null($data->creditos)) {
			$this->errorData = "Datos mal formados.";
			return false;
		}
		//Se valida que exista al menos un abono en ahorros o en créditos
		if(count($data->ahorros) == 0 && count($data->creditos)== 0) {
			$this->errorData = "No existen abonos.";
			return false;
		}
		return true;
	}

	/**
	 * Valida los ahorros
	 * @return type
	 */
	public function ahorrosValidos() {
		$data = json_decode($this->data);
		foreach($data->ahorros as $ahorro) {
			if(is_null($ahorro)) {
				$this->errorData = "Error en abonos de ahorros.";
				return false;
			}
			$validator = Validator::make((array)$ahorro, [
				'modalidad'		=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $this->entidad->id . ',es_reintegrable,1,deleted_at,NULL'
								],
				'valor'			=> 'bail|required|integer|min:1',
			]);
			if($validator->fails()) {
				$this->errorData = $validator->errors()->all()[0];
				return false;
			}
		}
		return true;
	}

	/**
	 * Valida los créditos
	 * @return type
	 */
	public function creditosValidos() {
		$data = json_decode($this->data);
		foreach($data->creditos as $credito) {
			if(is_null($credito)) {
				$this->errorData = "Error en abonos de créditos.";
				return false;
			}
			$validator = Validator::make((array)$credito, [
				'id'			=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.creditos.solicitudes_creditos,id,entidad_id,' . $this->entidad->id . ',tercero_id,' . $this->tercero . ',estado_solicitud,DESEMBOLSADO,deleted_at,NULL'
								],
				'capital'		=> 'bail|required|integer|min:0',
				'intereses'		=> 'bail|required|integer',
				'seguro'		=> 'bail|required|integer',
			]);
			if($validator->fails()) {
				$this->errorData = $validator->errors()->all()[0];
				return false;
			}
			$solicitud = null;
			if(($credito->capital + $credito->intereses + $credito->seguro) <= 0) {
				$solicitud = SolicitudCredito::find($credito->id);
				$patron = "Error en abono de obligación '%s', el abono corresponde a cero pesos.";
				$this->errorData = sprintf($patron, $solicitud->numero_obligacion);
				return false;
			}
			$res = DB::select('select creditos.fn_saldo_obligacion(?, ?, ?) AS saldo_obligacion', [$this->tercero, $credito->id, '3000/12/31']);
			$saldoObligacion = count($res) ? intval($res[0]->saldo_obligacion) : 0;
			$saldoObligacion -= $credito->capital;
			if($saldoObligacion < 0) {
				if(is_null($solicitud)) $solicitud = SolicitudCredito::find($credito->id);
				$patron = "La obligación '%s' quedaría con saldo negativo después del abono.";
				$this->errorData = sprintf($patron, $solicitud->numero_obligacion);
				return false;
			}
		}
		return true;
	}
}
