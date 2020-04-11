<?php

namespace App\Http\Requests\Ahorros\SDAT;

use App\Models\Contabilidad\Cuif;
use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

use \Carbon\Carbon;

class SaldarSDATRequest extends FormRequest
{
	use FonadminTrait;

	private $sdat;

	public function __construct(Route $route) {
		$this->sdat = $route->obj;
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
		return [
			'fechaDevolucion' => [
				'bail',
				'required',
				'date_format:"d/m/Y"',
				'modulocerrado:2',
				'modulocerrado:6',
			],
			'cuenta' => [
				'bail',
				'required',
				'integer',
				'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,deleted_at,NULL'
			]
		];
	}

	public function withValidator($validator) {
		if(!empty($validator->errors()->getMessages())) return null;
		$validator->after(function ($validator) {
			$cuif = Cuif::find($this->cuenta);

			if($cuif->modulo_id != 1 && $cuif->modulo_id != 2) {
				$validator->errors()->add('cuenta', "La cuenta seleccionada no es válida");
			}

			$fecha = Carbon::createFromFormat('d/m/Y', $this->fechaDevolucion)->startOfDay();
			$movimientos = $this->sdat
				->movimientosSdat()
				->where('fecha_movimiento', '>', $fecha)
				->count();

			$rendimientos = $this->sdat
				->rendimientosSdat()
				->where('fecha_movimiento', '>', $fecha)
				->count();

			if($movimientos || $rendimientos) {
				$msg = "El deposito presenta movimientos de capital o intereses posteriores a la fecha de devolución";
				$validator->errors()->add('fechaDevolucion', $msg);
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
			'required' => 'La :attribute es requerida',
			'fechaDevolucion.modulocerrado' => 'Módulo de contabilidad o ahorros cerrado para la fecha de radicación.',
		];
	}

	public function attributes() {
		return [
			'fechaDevolucion' => 'fecha de devolucion'
		];
	}
}
