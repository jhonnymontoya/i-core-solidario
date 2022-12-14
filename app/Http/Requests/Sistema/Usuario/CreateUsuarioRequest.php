<?php

namespace App\Http\Requests\Sistema\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class CreateUsuarioRequest extends FormRequest
{
    public function __construct() {
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
        return [
            'avatar' => 'bail|nullable',
            'tipo_identificacion_id' => [
                'bail',
                'required',
                'exists:sqlsrv.general.tipos_identificacion,id,deleted_at,NULL'
            ],
            'identificacion' => [
                'bail',
                'required',
                'integer',
                'unique:sqlsrv.sistema.usuarios,identificacion,NULL,id,deleted_at,NULL'
            ],
            'usuario' => [
                'bail',
                'required',
                'regex:/^[a-zA-Z0-9]*$/',
                'min:3',
                'max:20',
                'unique:sqlsrv.sistema.usuarios,usuario,NULL,id,deleted_at,NULL'
            ],
            'password' => 'bail|required|string|min:6|max:20',
            'confirmar_password' => 'bail|required|same:password',
            'primer_nombre' => 'bail|required|string|min:2|max:100',
            'segundo_nombre' => 'bail|nullable|string|min:2|max:100',
            'primer_apellido' => 'bail|required|string|min:2|max:100',
            'segundo_apellido' => 'bail|nullable|string|min:2|max:100',
            'email' => [
                'bail',
                'required',
                'email:rfc,dns',
                'min:3',
                'max:100',
                'unique:sqlsrv.sistema.usuarios,email,NULL,id,deleted_at,NULL'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'avatar.dimensions'         => 'El tama??o de la :attribute debe ser m??nimo de 160 x 160 pixeles',
            'avatar.image'              => 'La :attribute debe ser una im??gen v??lida (jpeg, png, bmp, gif o svg)',
            'password.min'              => 'La :attribute debe tener al menos :min caracteres',
            'confirmar_password.same'   => 'El campo :attribute y :other deben ser iguales',
            'usuario.regex'             => 'El :attribute no debe contener caracteres especiales',
        ];
    }

    public function attributes() {
        return [
            'avatar'                    => 'foto',
            'tipo_identificacion_id'    => 'tipo de identificaci??n',
            'password'                  => 'contrase??a',
            'confirmar_password'        => 'confirmar contrase??a',
            'primer_nombre'             => 'primer nombre',
            'segundo_nombre'            => 'segundo nombre',
            'primer_apellido'           => 'primer apellido',
            'segundo_apellido'          => 'segundo nombre',
            'email'                     => 'correo electr??nico',
        ];
    }
}
