<?php

namespace App\Http\Requests\Reportes\ConfiguracionExtractoSocial;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateConfiguracionExtractoSocialRequest extends FormRequest
{
    use ICoreTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $entidad = $this->getEntidad();
        return [
            'anio' => [
                'bail',
                'required',
                'integer',
                'between:2010,3000',
                'unique:sqlsrv.reportes.configuraciones_extracto_social,anio,NULL,id,entidad_id,' . $entidad->id . ',deleted_at,NULL'
            ],
            'fecha_inicio_socio_visible' => [
                'bail',
                'required',
                'date_format:"d/m/Y"',
                'before:fecha_fin_socio_visible'
            ],
            'fecha_fin_socio_visible' => [
                'bail',
                'required',
                'date_format:"d/m/Y"',
                'after:fecha_inicio_socio_visible'
            ],
            'tasa_promedio_ahorros_externa' => [
                'bail',
                'required',
                'numeric',
                'gt:0'
            ],
            'tasa_promedio_creditos_externa' => [
                'bail',
                'required',
                'numeric',
                'gt:0'
            ],
            'gasto_social_total' => [
                'bail',
                'required',
                'integer',
                'gt:0'
            ],
            'gasto_social_individual' => [
                'bail',
                'required',
                'integer',
                'gt:0'
            ],
            'mensaje_general' => [
                'bail',
                'required',
                'string',
                'min:10'
            ],
            'mensaje_ahorros' => [
                'bail',
                'required',
                'string',
                'min:10'
            ],
            'mensaje_creditos' => [
                'bail',
                'required',
                'string',
                'min:10'
            ],
            'mensaje_convenios' => [
                'bail',
                'required',
                'string',
                'min:10'
            ],
            'mensaje_inversion_social' => [
                'bail',
                'required',
                'string',
                'min:10'
            ]
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'anio.unique' => 'Ya se encuentra una configuración para el :attribute'
        ];
    }

    public function attributes()
    {
        return [
            'anio' => 'año'
        ];
    }

}
