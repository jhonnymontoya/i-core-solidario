<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OficialCumplimientoController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    /**
     * Lista los recursos
     */
    public function index(Request $request)
    {
        echo "index";
    }

    /**
     * Recupera un recurso
     */
    public function create()
    {
        echo "create";
    }

    /**
     * Crea un recurso
     */
    public function store(Request $request)
    {
        echo "store";
    }

    /**
     * Modifica un recurso
     */
    public function edit(OficialCumplimiento $obj, Request $request)
    {
        echo "edit";
    }

    /**
     * Elimina un recurso
     */
    public function update(Modelo $obj)
    {
        echo "update";
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::get(
            'oficialCumplimiento',
            'ControlVigilancia\OficialCumplimientoController@index'
        );

        Route::get(
            'oficialCumplimiento/create',
            'ControlVigilancia\OficialCumplimientoController@create'
        );

        Route::post(
            'oficialCumplimiento',
            'ControlVigilancia\OficialCumplimientoController@store'
        );

        Route::get(
            'oficialCumplimiento/{obj}/edit',
            'ControlVigilancia\OficialCumplimientoController@edit'
        )->name('oficialCumplimiento.edit');

        Route::put(
            'oficialCumplimiento/{obj}',
            'ControlVigilancia\OficialCumplimientoController@update'
        );
    }
}
