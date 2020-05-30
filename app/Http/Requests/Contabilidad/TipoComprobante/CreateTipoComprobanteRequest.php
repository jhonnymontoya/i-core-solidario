<?php

namespace App\Http\Requests\Contabilidad\TipoComprobante;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTipoComprobanteRequest extends FormRequest
{

	use ICoreTrait;

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
			'nombre' => [
				'bail',
				'required',
				'string',
				'min:4',
				'max:100',
				'unique:sqlsrv.contabilidad.tipos_comprobantes,nombre,NULL,id,entidad_id,' . $entidad->id,
			],
			'codigo' => [
				'bail',
				'required',
				'string',
				'min:1',
				'max:5',
				'unique:sqlsrv.contabilidad.tipos_comprobantes,codigo,NULL,id,entidad_id,' . $entidad->id,
			],
			'plantilla_impresion' => 'bail|required|string|in:COMPROBANTECONTABLE,NOTACREDITO,NOTADEBITO,PAGOIMPUESTOS,RECIBOCAJA',
			'comprobante_diario' => 'bail|required|string|in:INGRESO,EGRESO,NOTACONTABLE',
			'tipo_consecutivo' => 'bail|required|string|in:A,B,C',
			'modulo_id' => [
				'bail',
				'required',
				'exists:sqlsrv.contabilidad.modulos,id,deleted_at,NULL'
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
			'modulo_id'			=> 'módulo',
		];
	}
}
