<?php

namespace App\Http\Requests\Tarjeta\TarjetaHabiente;

use App\Models\Ahorros\CuentaAhorro;
use App\Models\General\Tercero;
use App\Models\Tarjeta\Producto;
use App\Models\Tarjeta\Tarjeta;
use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTarjetaHabienteRequest extends FormRequest
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
		if($this->has('cupo')) {
			if($this->cupo == null) {
				$this->merge(['cupo' => 0]);
			}
		}
		$entidad = $this->getEntidad();
		$reglas = [
			'tercero_id'			=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.general.terceros,id,entidad_id,' . $entidad->id . ',tipo_tercero,NATURAL,esta_activo,1,deleted_at,NULL',
									],
			'producto_id'			=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.tarjeta.productos,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL',
									],
			'tarjeta_id'			=> [
										'bail',
										'required',
										'integer',
										'exists:sqlsrv.tarjeta.tarjetas,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
									]
		];

		$producto = Producto::find($this->producto_id);
		$tercero = Tercero::find($this->tercero_id);
		$tarjeta = Tarjeta::find($this->tarjeta_id);

		if(empty($producto) || empty($tercero) || empty($tarjeta))return $reglas;

		switch ($producto->modalidad) {
			case 'CUENTAAHORROS':
				$reglas['cuenta_ahorro_id'] = [
					'bail',
					'required',
					'integer',
				];
				if(empty($tercero->socio))return $reglas;
				$cuenta = CuentaAhorro::find($this->cuenta_ahorro_id);
				if(empty($cuenta))return $reglas;
				array_push($reglas['cuenta_ahorro_id'], 'exists:sqlsrv.ahorros.cuentas_ahorros,id,entidad_id,' . $entidad->id . ',titular_socio_id,' . $tercero->socio->id . ',fecha_cierre,NULL,estado,ACTIVA,deleted_at,NULL');
				if($cuenta->tarjetahabientes->count()) {
					array_push($reglas['cuenta_ahorro_id'], 'regex:/^$/');
				}
				break;
			case 'CREDITO':
				$reglas['cupo'] = 'bail|nullable|integer|min:0';
				if($producto->modalidadCredito->esta_activa != true || $producto->modalidadCredito->uso_para_tarjeta != true) {
					array_push($reglas['producto_id'], 'regex:/^$/');
				}
				break;
			case 'MIXTO':
				$reglas['cuenta_ahorro_id'] = [
					'bail',
					'required',
					'integer',
				];
				if(empty($tercero->socio))return $reglas;
				$cuenta = CuentaAhorro::find($this->cuenta_ahorro_id);
				if(empty($cuenta))return $reglas;
				array_push($reglas['cuenta_ahorro_id'], 'exists:sqlsrv.ahorros.cuentas_ahorros,id,entidad_id,' . $entidad->id . ',titular_socio_id,' . $tercero->socio->id . ',fecha_cierre,NULL,estado,ACTIVA,deleted_at,NULL');
				if($cuenta->tarjetahabientes->count()) {
					array_push($reglas['cuenta_ahorro_id'], 'regex:/^$/');
				}
				$reglas['cupo'] = 'bail|nullable|integer|min:0';
				if($producto->modalidadCredito->esta_activa != true || $producto->modalidadCredito->uso_para_tarjeta != true) {
					array_push($reglas['producto_id'], 'regex:/^$/');
				}
				break;
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
			'cuenta_ahorro_id.regex'		=> 'La :attribute ya ha sido vinculada con otro producto.',
			'producto_id.regex'				=> 'La modalidad del :attribute no esta activa o no es de uso exclusivo para el producto.',
			'tarjeta_id.required'			=> 'La :attribute es requerida.',
			'cuenta_ahorro_id.required'		=> 'La :attribute es requerida.',
		];
	}

	public function attributes() {
		return [
			'tercero_id'		=> 'tarjetahabiente',
			'producto_id'		=> 'producto',
			'tarjeta_id'		=> 'tarjeta',
			'cuenta_ahorro_id'	=> 'cuenta de ahorros'
		];
	}
}
