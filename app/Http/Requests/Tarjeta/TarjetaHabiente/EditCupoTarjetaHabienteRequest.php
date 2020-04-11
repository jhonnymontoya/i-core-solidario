<?php

namespace App\Http\Requests\Tarjeta\TarjetaHabiente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class EditCupoTarjetaHabienteRequest extends FormRequest
{
    private $tarjeta;

    public function __construct(Route $route)
    {
        $this->tarjeta = $route->obj;
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
            'cupo'  => [
                'bail',
                'required',
                'integer',
                'min:0'
            ]
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $solicitudCredito = $this->tarjeta->solicitudCredito;
            if ($solicitudCredito->valor_credito == $this->cupo) {
                $validator->errors()->add(
                    'cupo',
                    'Por favor modifique el cupo.'
                );
            } 
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
