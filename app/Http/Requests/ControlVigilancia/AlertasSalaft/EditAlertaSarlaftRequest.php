<?php

namespace App\Http\Requests\ControlVigilancia\AlertasSalaft;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditAlertaSarlaftRequest extends FormRequest
{
    private $obj;

    public function __construct(Route $route)
    {
        $this->obj = $route->obj;
    }

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
            'diario'    => 'required|boolean',
            'semanal'   => 'required|boolean',
            'mensual'   => 'required|boolean',
            'anual'     => 'required|boolean',
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
