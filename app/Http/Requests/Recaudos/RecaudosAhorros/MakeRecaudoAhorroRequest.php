<?php

namespace App\Http\Requests\Recaudos\RecaudosAhorros;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Socios\Socio;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Http\FormRequest;
use Validator;

class MakeRecaudoAhorroRequest extends FormRequest
{
	use ICoreTrait;

	private $errorData = "Error en la data";

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
			'socio'				=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.socios.socios,id,deleted_at,NULL'
									],
			'modalidad'			=> [
										'bail',
										'nullable',
										'integer',
										'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $entidad->id . ',es_reintegrable,1,deleted_at,NULL'
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
		//Se valida si los módulos de [ahorros, contabilidad, cartera] se encuentran cerrados para la fecha
		if($this->moduloCerrado(2, $this->fecha) || $this->moduloCerrado(6, $this->fecha) || $this->moduloCerrado(7, $this->fecha)) {
			array_push($reglas["fecha"], "regex:/^$/");
		}

		$correcto = true;
		$cantidadAjustes = 0;
		if(!empty($this->data)) {
			$data = json_decode($this->data);
			foreach($data->ahorros as $ahorro) {
				if(!is_null($ahorro)) {
					$validator = Validator::make((array)$ahorro, [
						'modalidad'		=> [
											'bail',
											'required',
											'integer',
											'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $entidad->id . ',es_reintegrable,1,deleted_at,NULL'
										],
						'valor'			=> 'bail|required|integer|min:1',
					]);
					if($validator->fails()) {
						$correcto = false;
						break;
					}
					$cantidadAjustes++;
				}
			}
			$tercero = null;
			if(!is_null($this->socio)) {
				$socio = Socio::find($this->socio);
				if($socio) {
					$tercero = $socio->tercero->id;
				}
			}
			foreach($data->creditos as $credito) {
				if(!is_null($credito)) {
					$validator = Validator::make((array)$credito, [
						'id'			=> [
											'bail',
											'required',
											'integer',
											'exists:sqlsrv.creditos.solicitudes_creditos,id,entidad_id,' . $entidad->id . ',tercero_id,' . $tercero . ',estado_solicitud,DESEMBOLSADO,deleted_at,NULL'
										],
						'capital'		=> 'bail|required|integer|min:0',
						'intereses'		=> 'bail|required|integer',
						'seguro'		=> 'bail|required|integer',
					]);
					if($validator->fails()) {
						$correcto = false;
						break;
					}
					if(($credito->capital + $credito->intereses + $credito->seguro) <= 0) {
						$correcto = false;
						break;
					}
					$res = DB::select('select creditos.fn_saldo_obligacion(?, ?, ?) AS saldo_obligacion', [$tercero, $credito->id, '3000/12/31']);
					$saldoObligacion = count($res) ? intval($res[0]->saldo_obligacion) : 0;
					$saldoObligacion -= $credito->capital;
					if($saldoObligacion < 0) {
						$patron = "La obligación '%s' quedaría con saldo negativo después del abono.";
						$solicitud = SolicitudCredito::find($credito->id);
						$this->errorData = sprintf($patron, $solicitud->numero_obligacion);
						$correcto = false;
						break;
					}
					$cantidadAjustes++;
				}
			}
		}
		if(!$correcto || $cantidadAjustes == 0) {
			array_push($reglas['data'], "regex:/^$/");
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
			if($this->getValorAbono() > $this->getSaldoModalidad($this->fecha)) {
				$validator->errors()->add('data', 'Saldo en ahorro no cubre el valor del abono');
			}
			if(($this->getSaldoModalidad('31/12/3000') - $this->getValorAbono()) < 0) {
				$validator->errors()->add('data', 'Saldo del ahorro no puede quedar en negativo');
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

	public function getValorAbono() {
		$abono = 0;
		$data = json_decode($this->data);
		foreach($data->ahorros as $ahorro) {
			if(!is_null($ahorro)) {
				$abono += $ahorro->valor;
			}
		}
		foreach($data->creditos as $credito) {
			if(!is_null($credito)) {
				$abono += ($credito->capital + $credito->intereses + $credito->seguro);
			}
		}
		$abono += $data->GMF;
		return $abono;
	}

	public function getSaldoModalidad($fecha) {
		$saldo = 0;
		$fechaConsulta = Carbon::createFromFormat('d/m/Y', $fecha)->startOfDay();
		$respuesta = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$this->socio, $this->modalidad, $fechaConsulta]);
		if(!empty($respuesta)) {
			$saldo = intval($respuesta[0]->saldo);
		}
		return $saldo;
	}
}
