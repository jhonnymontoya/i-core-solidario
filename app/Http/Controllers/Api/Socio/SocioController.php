<?php

namespace App\Http\Controllers\Api\Socio;

use Route;
use App\Api\Ahorros;
use App\Api\Creditos;
use App\Api\Recaudos;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\FonadminTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class SocioController extends Controller
{
    use FonadminTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'sendResetLinkEmail']);
    }

    /**
     * Retorna información del socio
     */
    public function socio(Request $request)
    {
        $usuario = $request->user();
        $socio = $this->getSocio($usuario);
        return response()->json($socio);
    }

    private function getSocio($usuario) {
        $socio = $usuario->socios[0];
        $tercero = $socio->tercero;
        $data = [
            "tipoIdentificacion" => $tercero->tipoIdentificacion->codigo,
            "identificacion" => $tercero->numero_identificacion,
            "nombre" => Str::Title($tercero->nombre_corto),
            "imagen" => $this->getImagen($socio),
            "ahorros" => Ahorros::getAhorros($socio),
            "creditos" => Creditos::getCreditos($socio),
            "recaudo" => Recaudos::getRecaudos($socio),
        ];
        return $data;
    }

    private function getImagen($socio) {
        $path = sprintf("public/asociados/%s", $socio->obtenerAvatar());
        $content = base64_encode(Storage::get($path));
        return $content;
    }

    /**
     * Establece las rutas
     */
    public static function routes()//use Route;
    {
        Route::get('1.0/socio', 'Api\Socio\SocioController@socio');
    }
}
