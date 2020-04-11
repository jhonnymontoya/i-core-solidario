<?php

namespace App\Http\Requests\Creditos\CobroAdministrativo;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditCobroAdministrativoRequest extends FormRequest
{

	use FonadminTrait;

	private $cobro;

	public function __construct(Route $route) {
		$this->cobro = $route->obj; 
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
			'codigo'			=> [
									'bail',
									'required',
									'string',
									'min:2',
									'max:5',
									'unique:sqlsrv.creditos.cobros_administrativos,codigo,' . $this->cobro->id . ',id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
									//'unique:sqlsrv.creditos.cobros_administrativos,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
								],
			'nombre'			=> 'bail|required|string|min:3|max:50',
			'efecto'			=> 'bail|required|string|in:DEDUCCIONCREDITO,ADICIONCREDITO',
			'destino_cuif_id'	=> [
									'bail',
									'required',
									'integer',
									'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL'
								],
			'esta_activo'		=> 'bail|required|boolean',
			'es_condicionado'	=> [
									'bail',
									'required',
									'boolean',
								],
			'condicion'			=> [
									'bail',
									'nullable',
									'required_if:es_condicionado,1',
									'string',
									'in:MONTO,PLAZO',
								],
			'base_cobro'		=> 'bail|nullable|required_if:es_condicionado,0|string|in:VALORCREDITO,VAORDESCUBIERTO',
			'factor_calculo'	=> 'bail|nullable|required_if:es_condicionado,0|string|in:VALORFIJO,PORCENTAJEBASE',
			'valor'				=> [
									'bail',
									'nullable',
									'required_if:es_condicionado,0',
									'numeric',
									'min:0.01',
								]
		];

		//Se adiciona regla al valor, si el factor de cálculo es porcentaje,
		//el valor máximo permitido es 100
		if(! is_null($this->factor_calculo)) {
			if($this->factor_calculo == 'PORCENTAJEBASE') {
				array_push($reglas["valor"], "max:100");
			}
		}

		//Se adiciona regla a la propiedad 'es_condicionada' para evitar 
		//que se ponga como no condicionada cuando existan sub condiciones
		if(! is_null($this->es_condicionado)) {
			if($this->es_condicionado == "0" && $this->cobro->rangoCondiciones->count() > 0) {
				array_push($reglas["es_condicionado"], "regex:/^$/");
			}
		}

		if($this->cobro->es_condicionado && $this->cobro->rangoCondiciones->count() > 0) {
			if($this->cobro->condicion != $this->condicion) {
				array_push($reglas["condicion"], "regex:/^$/");
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
			'destino_cuif_id.required'	=> 'La :attribute es requerida.',
			'valor.max'					=> 'El :attribute no debe superar :max para porcentaje de base.',
			'es_condicionado.regex'		=> 'No se puede cambiar ya que existen condiciones asociadas.',
			'condicion.regex'			=> 'No puede cambiar la condición ya que existen condiciones asociadas.',
		];
	}

	public function attributes() {
		return [
			'destino_cuif_id'	=> 'cuenta destino',
			'es_condicionado'	=> 'cobro condicionado',
			'base_cobro'		=> 'base de cobro',
			'factor_calculo'	=> 'factor de cálculo'
		];
	}
}
