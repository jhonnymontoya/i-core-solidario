<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ControlVigilancia\OficialCumplimiento;
use App\Http\Requests\ControlVigilancia\OficialCumplimiento\CreateOficialCumplimientoRequest;

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
    public function index()
    {
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        return view("controlVigilancia.oficialCumplimplimiento.index")
            ->withOficialCumplimiento($oficialCumplimiento);
    }

    /**
     * Recupera un recurso
     */
    public function create()
    {
        return view("controlVigilancia.oficialCumplimplimiento.create");
    }

    /**
     * Crea un recurso
     */
    public function store(CreateOficialCumplimientoRequest $request)
    {
        dd($request->all());
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
