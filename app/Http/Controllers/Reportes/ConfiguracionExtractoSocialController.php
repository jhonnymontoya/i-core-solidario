<?php

namespace App\Http\Controllers\Reportes;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Reportes\ConfiguracionExtractoSocial;

class ConfiguracionExtractoSocialController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request)
    {
        $this->log("Ingresó a la configuración de extracto social con los siguientes parámetros " . json_encode($request->all()));
        dd("index");
    }

    public function create()
    {
        $this->log("Ingresó a crear configuración de extracto social");
        dd("create");
    }

    public function store(Request $request)
    {
        $this->log("Creó configuración de extracto social con los siguientes parámetros " . json_encode($request->all()), "CREAR");
        dd("store");
    }

    public function edit(ConfiguracionExtractoSocial $obj)
    {
        $msg = "Ingresó a editar la configuración de extracto social '%s'";
        $this->log(sprintf($msg, $obj->id));
        $this->objEntidad($obj);
        dd("edit");
    }

    public function update(ConfiguracionExtractoSocial $obj, Request $request)
    {
        $msg = "Actalizó la configuración de extracto social '%s' con los siguientes parámetros %s";
        $msg = sprintf($msg, $obj->id, json_encode($request->all()));
        $this->log($msg, "ACTUALIZAR");
        $this->objEntidad($obj);
        dd("update");
    }

    public static function routes()
    {
        Route::get('extractoSocial', 'Reportes/ConfiguracionExtractoSocialController@index');
        Route::get('extractoSocial/create', 'Reportes/ConfiguracionExtractoSocialController@create');
        Route::post('extractoSocial', 'Reportes/ConfiguracionExtractoSocialController@store');
        Route::get('extractoSocial/{obj}/edit', 'Reportes/ConfiguracionExtractoSocialController@edit')->name("extractoSocial.edit");
        Route::put('extractoSocial/{obj}', 'Reportes/ConfiguracionExtractoSocialController@update');
    }
}
