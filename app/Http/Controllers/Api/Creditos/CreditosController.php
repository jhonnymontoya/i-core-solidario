<?php

namespace App\Http\Controllers\Api\Creditos;

use Route;
use Validator;
use App\Api\Creditos;
use Illuminate\Http\Request;
use App\Traits\ICoreTrait;
use App\Http\Controllers\Controller;
use App\Models\Creditos\SolicitudCredito;

class CreditosController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtiene los detalles del crédito solicitado
     */
    public function obtenerCredito(Request $request, SolicitudCredito $obj)
    {
        $usuario = $request->user();
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $this->validarPermisoCreditoUsuario($tercero, $obj);
        $log = "API: Usuario '%s' consultó el crédito '%s'";
        $log = sprintf($log, $usuario->usuario, $obj->numero_obligacion);
        $this->log($log, 'CONSULTAR');

        $data = Creditos::getDetalleCredito($tercero, $obj);
        return response()->json($data);
    }

    public function obtenerModalidades(Request $request)
    {
        $usuario = $request->user();
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $log = "API: Usuario '%s' consultó las modalidades de crédito";
        $log = sprintf($log, $usuario->usuario);
        $this->log($log, 'CONSULTAR');

        $data = Creditos::getModalidadesDeCredito($tercero->entidad_id);
        return response()->json($data);
    }

    /**
     * Valida si el usuario tiene permiso para ver los movimientos
     * de ahorro de la modalidad seleccionada
     */
    private function validarPermisoCreditoUsuario($tercero, $solicitudCredito)
    {
        $log = "API ERROR: Usuario '%s' intentó consultar el crédito '%s'";
        $log = sprintf(
            $log,
            $tercero->numero_identificacion,
            $solicitudCredito->numero_obligacion
        );
        if($tercero->entidad_id != $solicitudCredito->entidad_id) {
            $this->log($log, 'CONSULTAR');
            return abort(401, 'No está autorizado a ingresar a la información');
        }

        if($solicitudCredito->estado_solicitud != 'DESEMBOLSADO') {
            $this->log($log, 'CONSULTAR');
            return abort(401, 'No está autorizado a ingresar a la información');
        }

        if($solicitudCredito->tercero_id != $tercero->id) {
            $this->log($log, 'CONSULTAR');
            return abort(401, 'No está autorizado a ingresar a la información');
        }
    }

    public function simularCredito(Request $request)
    {
        $usuario = $request->user();
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        Validator::make($request->all(), [
            "modalidadCreditoId" => [
                "bail",
                "required",
                "integer",
                "exists:sqlsrv.creditos.modalidades,id,entidad_id," . $tercero->entidad_id . ',esta_activa,1,uso_socio,1,deleted_at,NULL',
            ],
            "periodicidadDePago" => [
                "bail",
                "required",
                "string",
                "in:ANUAL,SEMESTRAL,CUATRIMESTRAL,TRIMESTRAL,BIMESTRAL,MENSUAL,QUINCENAL,CATORCENAL,DECADAL,SEMANAL,DIARIO"
            ],
            "valorCredito" => "bail|required|integer|min:1",
            "plazo" => "bail|required|integer|min:1|max:1000"
        ])->validate();

        $log = "API: Usuario '%s' simuló crédito con los siguientes parámetros '%s'";
        $log = sprintf($log, $usuario->usuario, json_encode($request->all()));
        $this->log($log, 'CONSULTAR');

        $data = Creditos::simularCredito(
            $socio,
            $request->modalidadCreditoId,
            $request->periodicidadDePago,
            $request->valorCredito,
            $request->plazo
        );
        return response()->json($data);
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get('1.0/credito/modalidades', 'Api\Creditos\CreditosController@obtenerModalidades');
        Route::post('1.0/credito/simular', 'Api\Creditos\CreditosController@simularCredito');
        Route::get('1.0/credito/{obj}', 'Api\Creditos\CreditosController@obtenerCredito');
    }
}
