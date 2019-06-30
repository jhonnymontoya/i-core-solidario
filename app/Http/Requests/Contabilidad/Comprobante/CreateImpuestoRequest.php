<?php

namespace App\Http\Requests\Contabilidad\Comprobante;

use App\Traits\FonadminTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateImpuestoRequest extends FormRequest
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
        $e = $this->getEntidad();
        return [
            "tipoImpuesto" => "bail|required|string|in:NACIONAL,DISTRITAL,REGIONAL",
            "impuesto" => [
                "bail",
                "required",
                "integer",
                "exists:sqlsrv.contabilidad.impuestos,id,entidad_id,$e->id,esta_activo,1,deleted_at,NULL"
            ],
            "concepto" => [
                "bail",
                "required",
                "integer",
                "exists:sqlsrv.contabilidad.conceptos_impuestos,id,impuesto_id,$this->impuesto,esta_activo,1,deleted_at,NULL"
            ],
            "tercero" => [
                "bail",
                "required",
                "integer",
                "exists:sqlsrv.general.terceros,id,entidad_id,$e->id,esta_activo,1,deleted_at,NULL"
            ],
            "base" => "bail|required|integer",
            "iva" => "bail|required|integer"
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator) {
            if (empty($this->base)) {
                $validator->errors()->add('base', 'La base no puede ser cero');
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
        ];
    }

    public function attributes() {
        return [
        ];
    }
}
