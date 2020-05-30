<?php

namespace App\Http\Requests\Socio\RetiroSocio;

use App\Traits\ICoreTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateRetiroSocioRequest extends FormRequest
{
    use ICoreTrait;

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
		$entidad = $this->getEntidad();
        return [
            'socio_id'                      => [
                                                    'bail',
                                                    'required',
                                                    'exists:sqlsrv.socios.socios,id,estado,ACTIVO,deleted_at,NULL',
                                            ],
            'causa_retiro_id'               => [
                                                    'bail',
                                                    'required',
                                                    'exists:sqlsrv.socios.causas_retiro,id,entidad_id,' . $entidad->id . ',esta_activo,1,deleted_at,NULL'
                                            ],
            'fecha_solicitud_retiro'        => 'bail|required|date_format:"d/m/Y"',
            'observacion'                   => 'bail|nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'socio_id.exists'       => 'Estado del asociado no es válido.'
        ];
    }

    public function attributes() {
        return [
        ];
    }
}
