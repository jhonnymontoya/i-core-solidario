<?php

namespace App\Http\Requests\Ahorros\AjusteAhorros;

use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class CreateAjusteAhorrosRequest extends FormRequest
{
	use FonadminTrait;

	public function __construct(Route $route) {
		//$this->socio = $route->obj;
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
	public function rules() {//dd($this->all());
		$entidad = $this->getEntidad();
		$reglas = [
			'socio'				=> [
										'bail',
										'required',
								],
			'fechaAjuste'		=> 'bail|required|date_format:"d/m/Y"',
			'modalidadId'		=> [
										'bail',
										'required',
										'exists:sqlsrv.ahorros.modalidades_ahorros,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'naturalezaAjusteAhorros'	=> 'bail|required|in:AUMENTO,DECREMENTO',
			'valorAjuste'		=> [
									'bail',
									'required_without:valorAjusteIntereses',
									'numeric',
									'min:0',
								],
			'naturalezaAjusteIntereses'	=> 'bail|required|in:AUMENTO,DECREMENTO',
			'valorAjusteIntereses'=> [
									'bail',
									'required_without:valorAjuste',
									'numeric',
									'min:0',
								],
			'cuifId'			=> [
				'bail',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL',
			],
			'observaciones'		=> 'bail|required|string|min:4|max:1000',
			'terceroContrapartidaId' => [
				'bail',
				'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
			],
			'referencia'		=> 'bail|nullable|string|max:100',
		];

		$socio = Socio::find($this->socio);

		if($socio == null) {
			array_push($reglas['socio'], 'exists:sqlsrv.socios.socios,id,deleted_at,NULL');
		}
		else if($socio->tercero->entidad->id != $entidad->id) {
			array_push($reglas['socio'], 'max:-1');
		}

		if(empty($this->valorAjuste) && empty($this->valorAjusteIntereses)) {
			array_push($reglas['valorAjuste'], 'regex:/^$/');
			array_push($reglas['valorAjusteIntereses'], 'regex:/^$/');
			return $reglas;
		}

		$valorAhorros = floatval("$this->valorAjuste");			
		$valorAhorros = $this->naturalezaAjusteAhorros == 'AUMENTO' ? $valorAhorros : -$valorAhorros;
		$valorIntereses = floatval("$this->valorAjusteIntereses");			
		$valorIntereses = $this->naturalezaAjusteIntereses == 'AUMENTO' ? $valorIntereses : -$valorIntereses;
		$valorAjuste = $valorAhorros + $valorIntereses;
		if($valorAjuste == 0) {
			array_push($reglas['cuifId'], 'nullable');
			array_push($reglas['terceroContrapartidaId'], 'nullable');
		}
		else {
			array_push($reglas['cuifId'], 'required');
			array_push($reglas['terceroContrapartidaId'], 'required');
		}
		
		$resp = DB::select('exec ahorros.sp_seleccion_modalidades_ahorros ?, ?', [$entidad->id, $socio->id]);
		$modalidades = "";
		foreach($resp as $res)$modalidades .= $res->id . ',';
		$modalidades = substr($modalidades, 0, strlen($modalidades) - 1);
		array_push($reglas['modalidadId'], 'in:' . $modalidades);

		$resp = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$socio->id, $this->modalidadId, '01/01/3000']);
		$saldo = floatval($resp[0]->saldo);
		$intereses = floatval($resp[0]->intereses);

		if(!empty($this->valorAjuste)) {
			if(($saldo + $valorAhorros) < 0) {
				array_push($reglas['valorAjuste'], 'max:-1');
			}
		}

		if(!empty($this->valorAjusteIntereses)) {
			if(($intereses + $valorIntereses) < 0) {
				array_push($reglas['valorAjusteIntereses'], 'max:-1');
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
			'socio.max'						=> 'El :attribute no existe',
			'valorAjuste.min'				=> 'El :attribute debe ser mayor a cero (0)',
			'valorAjuste.max'				=> 'El saldo de la modalidad no puede ser negativo',
			'valorAjusteIntereses.max'		=> 'El saldo de la modalidad no puede ser negativo',
			'valorAjuste.regex'				=> 'Se requiere al menos un valor para el ajuste',
			'valorAjusteIntereses.regex'	=> 'Se requiere al menos un valor para el ajuste'
		];
	}

	public function attributes() {
		return [
			'valorAjuste'			=> 'ajuste ahorro',
			'valorAjusteIntereses'	=> 'ajuste intereses',
			'cuifId'				=> 'cuenta contrapartida',
			'terceroContrapartidaId'=> 'tercero contrapartida'
		];
	}
}
