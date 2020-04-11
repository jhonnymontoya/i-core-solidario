<?php

namespace App\Http\Requests\Tarjeta\Producto;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductoRequest extends FormRequest
{
	use FonadminTrait;

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
		return [
			'codigo'								=> [
				'bail',
				'required',
				'string',
				'min:2',
				'max:5',
				'unique:sqlsrv.tarjeta.productos,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
			],
			'nombre'								=> 'bail|required|string|min:5|max:50',
			'credito'								=> 'bail|required_without_all:ahorro,vista|boolean',
			'ahorro'								=> 'bail|required_without_all:credito,vista|boolean',
			'vista'									=> 'bail|required_without_all:credito,ahorro|boolean',
			'convenio'								=> 'bail|required|string|min:1|max:10',
			'tipo_pago_cuota_manejo'				=> 'bail|required|string|in:VENCIDO',
			'valor_cuota_manejo_mes'				=> 'bail|nullable|required_with:periodicidad_cuota_manejo,meses_sin_cuota_manejo,cuota_manejo_cuif_id|numeric|min:1',
			'periodicidad_cuota_manejo'				=> 'bail|nullable|required_with:valor_cuota_manejo_mes,meses_sin_cuota_manejo,cuota_manejo_cuif_id|string|in:MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
			'meses_sin_cuota_manejo'				=> 'bail|nullable|required_with:valor_cuota_manejo_mes,periodicidad_cuota_manejo,cuota_manejo_cuif_id|integer|min:1',
			'modalidad_credito_id'					=> [
				'bail',
				'nullable',
				'required_if:credito,1',
				'integer',
				'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $entidad->id . ',esta_activa,1,uso_para_tarjeta,1,deleted_at,NULL',
			],
			'cuenta_compensacion_cuif_id'			=> [
				'bail',
				'required',
				'integer',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,modulo_id,2,deleted_at,NULL',
			],
			'ingreso_comision_cuif_id'				=> [
				'bail',
				'required',
				'integer',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,modulo_id,2,deleted_at,NULL',
			],
			'egreso_comision_cuif_id'				=> [
				'bail',
				'required',
				'integer',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,modulo_id,2,deleted_at,NULL',
			],
			'cuota_manejo_cuif_id'					=> [
				'bail',
				'nullable',
				'required_with:valor_cuota_manejo_mes,periodicidad_cuota_manejo,meses_sin_cuota_manejo',
				'integer',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,modulo_id,2,deleted_at,NULL',
			],
			'numero_retiros_sin_cobro_red'			=> 'bail|nullable|integer|min:1',
			'numero_retiros_sin_cobro_otra_red'		=> 'bail|nullable|integer|min:1',
		];
	}

	public function withValidator($validator) {
		if(is_null($this->ahorro) && empty($this->ahorro)) return null;
		if(is_null($this->vista) && empty($this->vista)) return null;

		$validator->after(function ($validator) {
			if($this->ahorro and $this->vista) {
				$mensaje = "No se puede asociar dos modalidades de ahorro a un producto";
				$validator->errors()->add('credito', $mensaje);
			}
		});
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages() {
		return [
			'required_with'		=> 'El campo es requerido para completar la parametrización.',
			'modalidad_credito_id.required_if' => 'Se require que especifique una modalidad de crédito.'
		];
	}

	public function attributes() {
		return [
		];
	}
}
