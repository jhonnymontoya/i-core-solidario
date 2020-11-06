<?php

namespace App\Http\Requests\ControlVigilancia\OficialCumplimiento;

use Illuminate\Foundation\Http\FormRequest;

class CreateOficialCumplimientoRequest extends FormRequest
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
            'nombre'    => 'required|string|min:6|max:255',
            'email'     => 'required|email|max:255',
            'emailcc'   => 'nullable|email|max:255'
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
            'email'     => 'correo electrónico',
            'emailcc'   => 'copia correo electrónico'
        ];
    }
}
