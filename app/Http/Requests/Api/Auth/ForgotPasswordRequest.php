<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
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
        return [
            'usuario'   => [
                'bail',
                'required',
                'string',
                'min:3',
                'exists:sqlsrv.sistema.usuarios_web,usuario,esta_activo,1,deleted_at,NULL'
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
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
