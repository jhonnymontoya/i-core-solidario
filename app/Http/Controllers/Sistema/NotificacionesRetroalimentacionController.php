<?php

namespace App\Http\Controllers\Sistema;

use Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sistema\NotificacionRetroalimentacion;

class NotificacionesRetroalimentacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['postSNS']);
        $this->middleware('verEnt')->except(['postSNS']);
        $this->middleware('verMenu')->except(['postSNS']);
    }

    public function index(Request $request)
    {
        $notificaciones = NotificacionRetroalimentacion::all();
        dd("Entro", $notificaciones);
    }

    public function postSNS(Request $request)
    {
        $trama = json_encode($request->all());
        $obj = NotificacionRetroalimentacion::create(["trama" => $trama]);
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get(
            'notificacionesRetroalimentacion',
            'Sistema/NotificacionesRetroalimentacionController@index'
        );
        Route::post(
            'notificacionesRetroalimentacion/sns',
            'Sistema/NotificacionesRetroalimentacionController@postSNS'
        );
    }
}
