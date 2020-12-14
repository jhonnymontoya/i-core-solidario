<?php

namespace App\Http\Controllers\Api\Ahorros;

use Route;
use App\Api\Ahorros;
use Illuminate\Http\Request;
use App\Traits\ICoreTrait;
use App\Http\Controllers\Controller;
use App\Models\Ahorros\ModalidadAhorro;

class AhorrosController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtiene los ahorros
     */
    public function obtenerAhorro(Request $request, ModalidadAhorro $obj)
    {
        $usuario = $request->user();
        $socio = $usuario->socios[0];
        $this->validarPermisoModalidadUsuario($socio, $obj);
        $log = "API: Usuario '%s' consultó los ahorros de la modalidad '%s'";
        $log = sprintf($log, $usuario->usuario, $obj->nombre);
        $this->log($log, 'CONSULTAR');

        $data = Ahorros::getDetalleAhorros($socio, $obj);
        return response()->json($data);
    }

    /**
     * Valida si el usuario tiene permiso para ver los movimientos
     * de ahorro de la modalidad seleccionada
     */
    private function validarPermisoModalidadUsuario($socio, $modalidad)
    {
        $tercero = $socio->tercero;
        if($tercero->entidad_id != $modalidad->entidad_id) {
            $log = "API ERROR: Usuario '%s' intentó consultar los ahorros de la modalidad '%s'";
            $log = sprintf(
                $log,
                $tercero->numero_identificacion,
                $modalidad->nombre
            );
            $this->log($log, 'INGRESAR');
            return abort(401, 'No está autorizado a ingresar a la información');
        }
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get('1.0/ahorro/{obj}', 'Api\Ahorros\AhorrosController@obtenerAhorro');
    }
}
