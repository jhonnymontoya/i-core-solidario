<?php

namespace App\Http\Requests\Ahorros\TipoCuotaAhorros;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateTipoCuotaAhorrosRequest extends FormRequest
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
        $reglas = [
            'codigo' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:5',
                'unique:sqlsrv.ahorros.modalidades_ahorros,codigo,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL',
            ],
            'nombre' => 'bail|required|string|min:2|max:100',
            'cuif_id' => [
                'bail',
                'required',
                'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL',
            ],
            'intereses_cuif_id' => [
                'bail',
                'nullable',
                'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,2,deleted_at,NULL',
            ],
            'intereses_por_pagar_cuif_id' => [
                'bail',
                'nullable',
                'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,6,deleted_at,NULL',
            ],
            'tipo_ahorro' => 'bail|required|in:PROGRAMADO,VOLUNTARIO',
            'tasa' => 'bail|nullable|numeric|gt:0|max:100',
            'periodicidad_interes' => 'bail|nullable|required_with:tasa|in:DIARIO,SEMANAL,DECADAL,CATORCENAL,QUINCENAL,MENSUAL,BIMESTRAL,TRIMESTRAL,CUATRIMESTRAL,SEMESTRAL,ANUAL',
            'capitalizacion_simultanea' => 'bail|required|boolean',
            'paga_retiros' => 'bail|nullable|boolean',
            'paga_intereses_retirados' => 'bail|nullable|boolean',
            'para_beneficiario' => 'bail|required|boolean',
        ];

        if($this->tipo_ahorro == 'PROGRAMADO') {
            $reglasProgramado =  [
                'tipo_vencimiento' => 'bail|nullable|required_if:tipo_ahorro,PROGRAMADO|in:COLECTIVO,INDIVIDUAL',
                'plazo' => 'bail|nullable|required_if:tipo_vencimiento,INDIVIDUAL|integer|min:1|max:120',
                'fecha_vencimiento_colectivo' => 'bail|nullable|required_if:tipo_vencimiento,COLECTIVO|date_format:"d/m/Y"|after:tomorrow',
                'tasa_penalidad' => 'bail|nullable|required_if:tipo_ahorro,PROGRAMADO|numeric|min:0.00001|max:100',
                'penalidad_por_retiro' => 'bail|nullable|required_if:tipo_ahorro,PROGRAMADO|boolean',

            ];

            $reglas = array_merge($reglas, $reglasProgramado);
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
            'cuif_id.required'      => 'La :attribute es requerida',
        ];
    }

    public function attributes() {
        return [
            'cuif_id'                       => 'cuenta contable capital',
            'intereses_cuif_id'             => 'cuenta contable intereses',
            'intereses_por_pagar_cuif_id'   => 'cuenta contable intereses por pagar',
            'paga_intereses_retirados'      => 'incluye retirados en el cálculo de rendimientos',
        ];
    }
}
