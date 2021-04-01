<?php

namespace App\Http\Requests\Sistema\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class EditUiConfiguracionRequest extends FormRequest
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
            'clase'     => 'bail|nullable|string|in:sidebar-collapse',
            'tema'      => 'bail|required|string|in:skin-blue,skin-black,skin-red,skin-yellow,skin-purple,skin-green,skin-blue-light,skin-black-light,skin-red-light,skin-yellow-light,skin-purple-light,skin-green-light'
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
