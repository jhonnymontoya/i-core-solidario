<?php

namespace App\Http\Requests\Consulta\Consulta;

use App\Traits\ICoreTrait;
use App\Models\Creditos\Modalidad;
use Illuminate\Foundation\Http\FormRequest;

class CreateSolicitudCreditoRequest extends FormRequest
{
    use ICoreTrait;

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
            'modalidad' => [
                'bail',
                'required',
                'integer',
                'exists:sqlsrv.creditos.modalidades,id,entidad_id,' . $this->getEntidad()->id . ',esta_activa,1,deleted_at,NULL',
            ],
            'valorCredito' => 'bail|required|integer|min:1',
            'plazo' => 'bail|required|integer|min:1|max:1000',
            'observaciones' => 'bail|nullable|string|min:6|max:2000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
            if($validator->errors()->count() == 0) {
                $modalidad = Modalidad::find($this->modalidad);
                if($modalidad->estaParametrizada() == false) {
                    $validator->errors()->add('modalidad', 'La modalidad seleccionada no es válida.');
                }
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
            'modalidad.required' => 'La :attribute es requerida.',
            'valorCredito.required' => 'El :attribute es requerido.',
            'plazo.required' => 'El :attribute es requerido.',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
