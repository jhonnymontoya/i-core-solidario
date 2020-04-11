<?php

namespace App\Http\Requests\ControlVigilancia\ArhivosSES;

use Illuminate\Foundation\Http\FormRequest;

class DescargarReporteRequest extends FormRequest
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
        $reportes = array(
            'ASOCIADOS',
            'CATALOGOCUENTAS',
            'DIRECTIVOS',
            'APORTES',
            'CAPTACIONES',
            'CARTERACREDITOS'
        );
        $reportes = implode(",", $reportes);
        return [
            'fecha_reporte' => 'bail|required|date_format:"Y/m"',
            'reporte' => [
                            'bail',
                            'required',
                            'string',
                            'in:' . $reportes
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
        ];
    }

    public function attributes() {
        return [
        ];
    }
}
