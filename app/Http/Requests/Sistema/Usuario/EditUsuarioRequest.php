<?php

namespace App\Http\Requests\Sistema\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditUsuarioRequest extends FormRequest
{
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
                'unique:sqlsrv.sistema.usuarios,identificacion,' . $this->obj->id . ',id,deleted_at,NULL'
            ],
            'usuario' => [
                'bail',
                'required',
                'regex:/^[a-zA-Z0-9]*$/',
                'min:3',
                'max:20',
                'unique:sqlsrv.sistema.usuarios,usuario,' . $this->obj->id . ',id,deleted_at,NULL'
            ],
            'password' => 'bail|nullable|string|min:6|max:20',
            'confirmar_password' => 'bail|same:password',
            'primer_nombre' => 'bail|required|string|min:2|max:100',
            'segundo_nombre' => 'bail|nullable|string|min:2|max:100',
            'primer_apellido' => 'bail|required|string|min:2|max:100',
            'segundo_apellido' => 'bail|nullable|string|min:2|max:100',
            'email' => [
                'bail',
                'required',
                'email',
                'min:3',
                'max:100',
                'unique:sqlsrv.sistema.usuarios,email,' . $this->obj->id . ',id,deleted_at,NULL'
            ],
            'esta_activo' => 'bail|required|boolean',
            'entidades' => 'bail|nullable|array',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'avatar.dimensions' => 'El tamaño de la :attribute debe ser mínimo de 160 x 160 pixeles',
            'avatar.image' => 'La :attribute debe ser una imágen válida (jpeg, png, bmp, gif o svg)',
            'password.min' => 'La :attribute debe tener al menos :min caracteres',
            'confirmar_password.same' => 'El campo :attribute y :other deben ser iguales',
            'usuario.regex' => 'El :attribute no debe contener caracteres especiales',
        ];
    }

    public function attributes() {
        return [
            'avatar' => 'foto',
            'tipo_identificacion_id' => 'tipo de identificación',
            'password' => 'contraseña',
            'confirmar_password' => 'confirmar contraseña',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo nombre',
            'email' => 'correo electrónico',
        ];
    }
}
