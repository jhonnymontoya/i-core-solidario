<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
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

    public function index()
    {
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        return view("controlVigilancia.oficialCumplimplimiento.index")
            ->withOficialCumplimiento($oficialCumplimiento);
    }

    public function create()
    {
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->count();

        if($oficialCumplimiento > 0) {
            Session::flash("error", "Ya existe un oficial de cumplimiento");
            return redirect("oficialCumplimiento");
        }
        return view("controlVigilancia.oficialCumplimplimiento.create");
    }

    public function store(CreateOficialCumplimientoRequest $request)
    {
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->count();

        if($oficialCumplimiento > 0) {
            Session::flash("error", "Ya existe un oficial de cumplimiento");
            return redirect("oficialCumplimiento");
        }

        $entidadId = $this->getEntidad()->id;
        $oficialCumplimiento = new OficialCumplimiento;
        $oficialCumplimiento->fill($request->all());
        $oficialCumplimiento->entidad_id = $entidadId;
        $oficialCumplimiento->email_copia = $request->emailcc;
        $oficialCumplimiento->save();

        Session::flash("message", "Se ha creado con éxito el oficial de cumplimiento");
        return redirect("oficialCumplimiento");
    }

    public function edit(OficialCumplimiento $obj, Request $request)
    {
        echo "edit";
    }

    public function update(Modelo $obj)
    {
        echo "update";
    }

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
