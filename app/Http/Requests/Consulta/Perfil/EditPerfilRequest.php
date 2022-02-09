<?php

namespace App\Http\Requests\Consulta\Perfil;

use Illuminate\Foundation\Http\FormRequest;

class EditPerfilRequest extends FormRequest
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
            'avatar' => 'bail|nullable',
            'password' => 'bail|nullable|string|min:6|max:20',
            'confirmar_password' => 'bail|same:password',
            'ciudad_laboral' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'exists:sqlsrv.general.ciudades,id,deleted_at,NULL',
            ],
            'direccion_laboral' => [
                'bail',
                'required',
                'string',
                'min:5',
                'max:255',
            ],
            'celular_laboral' => [
                'bail',
                'required',
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
                'required',
                'email:rfc,dns',
                'string',
                'max:200',
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
            'password.min'              => 'La :attribute no cumple con la longitud requerida',
            'password.max'              => 'La :attribute no debe contener más de :max caracteres',
            'confirmar_password.same'   => 'El campo :attribute y :other deben ser iguales',
        ];
    }

    public function attributes() {
        return [
            'password'              => 'contraseña',
            'confirmar_password'    => 'confirmar contraseña',
            'ciudad_laboral'        => 'ciudad',
            'direccion_laboral'     => 'dirección',
            'celular_laboral'       => 'celular',
            'telefono_laboral'      => 'teléfono',
            'extension_laboral'     => 'extensión',
            'email_laboral'         => 'correo electrónico',
        ];
    }
}
