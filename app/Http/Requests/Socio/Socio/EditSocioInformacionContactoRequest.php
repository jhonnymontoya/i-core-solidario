<?php

namespace App\Http\Requests\Socio\Socio;

use Illuminate\Foundation\Http\FormRequest;

class EditSocioInformacionContactoRequest extends FormRequest
{
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
        return [
            'ciudad_residencial' => [
                'bail',
                'nullable',
                'required_with:direccion_residencial,tipo_vivienda,estrato_vivienda,celular_residencia,telefono_residencia,email_residencia',
                'required_without:ciudad_laboral',
                'exists:sqlsrv.general.ciudades,id,deleted_at,NULL'
            ],
            'direccion_residencial' => [
                'bail',
                'nullable',
                'required_with:ciudad_residencial,tipo_vivienda,estrato_vivienda,celular_residencia,telefono_residencia,email_residencia',
                'string',
                'min:5',
                'max:255',
            ],
            'tipo_vivienda' => [
                'bail',
                'nullable',
                'exists:sqlsrv.socios.tipos_vivienda,id,deleted_at,NULL',
            ],
            'estrato_vivienda' => [
                'bail',
                'nullable',
                'integer',
                'min:1',
                'max:6',
            ],
            'celular_residencia' => [
                'bail',
                'nullable',
                'string',
                'min:10',
                'max:20',
            ],
            'telefono_residencia' => [
                'bail',
                'nullable',
                'string',
                'min:4',
                'max:20',
            ],
            'email_residencia' => [
                'bail',
                'nullable',
                'email:rfc,dns',
                'string',
                'max:200',
            ],
            'preferencia_envio_residencia' => 'bail|required|boolean',
            'ciudad_laboral' => [
                'bail',
                'nullable',
                'required_with:direccion_laboral,celular_laboral,telefono_laboral,extension_laboral,email_laboral',
                'exists:sqlsrv.general.ciudades,id,deleted_at,NULL',
            ],
            'direccion_laboral' => [
                'bail',
                'nullable',
                'required_with:ciudad_laboral,celular_laboral,telefono_laboral,extension_laboral,email_laboral',
                'string',
                'min:5',
                'max:255',
            ],
            'celular_laboral' => [
                'bail',
                'nullable',
                'string',
                'min:10',
                'max:20',
            ],
            'telefono_laboral' => [
                'bail',
                'nullable',
                'string',
                'min:4',
                'max:20',
            ],
            'extension_laboral' => [
                'bail',
                'nullable',
                'string',
                'min:1',
                'max:8',
            ],
            'email_laboral' => [
                'bail',
                'nullable',
                'email:rfc,dns',
                'string',
                'max:200',
            ],
            'preferencia_envio_laboral' => 'bail|required|boolean',
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
        ];
    }
}
