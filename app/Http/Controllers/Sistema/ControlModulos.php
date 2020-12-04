<?php

namespace App\Http\Controllers\Sistema;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Models\Sistema\Modulo;
use App\Models\General\Entidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Sistema\ControlModulos\EditControlModuloRequest;

class ControlModulos extends Controller
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
        $this->log('Ingresó a control de módulos');
        $modulos = Modulo::with([
            "entidad",
            "entidad.terceroEntidad",
        ])
        ->search($request->name)
        ->estado($request->estado);

        if(is_null($request->entidad) == false){
            $modulos = $modulos->entidadId($request->entidad);
        }

        $modulos = $modulos->paginate();

        $entidades = Entidad::with('terceroEntidad')->get();

        $listaEntidades = array();
        foreach($entidades as $entidad)
            $listaEntidades[$entidad->id] = $entidad->terceroEntidad->nombre_corto;

        $estados = [
            true => 'Activo',
            false => 'Inactivo'
        ];

        return view('sistema.controlModulos.index')
            ->withModulos($modulos)
            ->withEntidades($listaEntidades)
            ->withEstados($estados);
    }

    /**
     * Recupera un recurso
     */
    public function edit(Modulo $obj)
    {
        $this->log('Ingresó a editar el control de módulos');
        return view('sistema.controlModulos.edit')->withModulo($obj);
    }

    /**
     * Crea un recurso
     */
    public function update(Modulo $obj, EditControlModuloRequest $request)
    {
        $msg = "Actualizó el control de módulo con los siguientes parámetros %s";
        $msg = sprintf($msg, json_encode($request->all()));
        $this->log($msg, 'ACTUALIZAR');

        $obj->esta_activo = $request->esta_activo;
        $obj->save();

        $msg = "Se actualizó con éxito el módulo '%s'";
        $msg = sprintf($msg, $obj->nombre);

        Session::flash("message", $msg);
        return redirect('controlModulos');

    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get('controlModulos', 'Sistema\ControlModulos@index');
        Route::get('controlModulos/{obj}', 'Sistema\ControlModulos@edit')
            ->name('controlModulos.edit');
        Route::put('controlModulos/{obj}', 'Sistema\ControlModulos@update');
    }
}
