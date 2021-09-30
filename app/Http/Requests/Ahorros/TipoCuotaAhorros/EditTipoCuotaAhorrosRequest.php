<?php

namespace App\Http\Requests\Ahorros\TipoCuotaAhorros;

use App\Traits\ICoreTrait;
use Illuminate\Routing\Route;
use Illuminate\Foundation\Http\FormRequest;

class EditTipoCuotaAhorrosRequest extends FormRequest
{
    use ICoreTrait;

    private $obj;

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
        $reglas =  [
            'nombre' => 'bail|required|string|min:2|max:100',
            'intereses_cuif_id' => [
                'bail',
                'nullable',
                'exists:sqlsrv.contabilidad.cuifs,id,entidad_id,' . $entidad->id . ',tipo_cuenta,AUXILIAR,esta_activo,1,modulo_id,2,deleted_at,NULL',
            ],
            'tasa' => 'bail|nullable|numeric|gt:0|max:100',
            'capitalizacion_simultanea' => 'bail|required|boolean',
            'esta_activa' => 'bail|required|boolean',
            'paga_intereses_retirados' => 'bail|nullable|boolean',
            'para_beneficiario' => 'bail|required|boolean',
        ];

        if($this->tipo_ahorro == 'PROGRAMADO') {
            $reglasProgramado =  [
                'tasa_penalidad' => 'bail|nullable|required_if:tipo_ahorro,PROGRAMADO|numeric|gte:0|max:100',
            ];

            if($this->obj->tipo_vencimiento == "COLECTIVO")
            {
                $reglasProgramado['fecha_vencimiento_colectivo'] = 'bail|required|date_format:"d/m/Y"|after:tomorrow';
            }

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
