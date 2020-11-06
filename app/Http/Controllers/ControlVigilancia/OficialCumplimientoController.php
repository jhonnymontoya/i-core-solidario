<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\ControlVigilancia\OficialCumplimiento;
use App\Http\Requests\ControlVigilancia\OficialCumplimiento\EditOficialCumplimientoRequest;
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
        $this->log('Ingresó a consultar el oficial de cumplimiento');
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        return view("controlVigilancia.oficialCumplimplimiento.index")
            ->withOficialCumplimiento($oficialCumplimiento);
    }

    public function create()
    {
        $this->log('Ingresó a crear el oficial de cumplimiento');
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
        $this->log("Creó el oficial de cumplimiento con los siguientes parámetros " . json_encode($request->all()), 'CREAR');
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

    public function edit()
    {
        $this->log('Ingresó a editar el oficial de cumplimiento');
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        if($oficialCumplimiento == null) {
            Session::flash("error", "No existe ningún oficial de cumplimiento");
            return redirect("oficialCumplimiento");
        }

        return view("controlVigilancia.oficialCumplimplimiento.edit")
            ->withOficialCumplimiento($oficialCumplimiento);
    }

    public function update(EditOficialCumplimientoRequest $request)
    {
        $this->log("Creó el oficial de cumplimiento con los siguientes parámetros " . json_encode($request->all()), 'ACTUALIZAR');
        $oficialCumplimiento = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        if($oficialCumplimiento == null) {
            Session::flash("error", "No existe ningún oficial de cumplimiento");
            return redirect("oficialCumplimiento");
        }

        $oficialCumplimiento->delete();

        $entidadId = $this->getEntidad()->id;
        $oficialCumplimiento = new OficialCumplimiento;
        $oficialCumplimiento->fill($request->all());
        $oficialCumplimiento->entidad_id = $entidadId;
        $oficialCumplimiento->email_copia = $request->emailcc;
        $oficialCumplimiento->save();

        Session::flash("message", "Se ha actualizado con éxito el oficial de cumplimiento");
        return redirect("oficialCumplimiento");
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
            'oficialCumplimiento/edit',
            'ControlVigilancia\OficialCumplimientoController@edit'
        );

        Route::put(
            'oficialCumplimiento',
            'ControlVigilancia\OficialCumplimientoController@update'
        );
    }
}
