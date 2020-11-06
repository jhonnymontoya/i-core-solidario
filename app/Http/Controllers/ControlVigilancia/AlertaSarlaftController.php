<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use Carbon\Carbon;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\ControlVigilancia\Alerta;
use App\Models\ControlVigilancia\OficialCumplimiento;
use App\Http\Requests\ControlVigilancia\AlertasSalaft\EditAlertaSarlaftRequest;

class AlertaSarlaftController extends Controller
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
        $this->log('Ingresó a consultar las alertas SARLAFT');
        $alertas = Alerta::entidadId()
            ->nombre($request->nombre)
            ->orderBy("nombre")
            ->get();

        $oficialCumplimientoCantidad = OficialCumplimiento::entidadId()
            ->activo()
            ->count();

        return view("controlVigilancia.alertasSarlaft.index")
            ->withAlertas($alertas)
            ->withCantidadOficialCumplimiento($oficialCumplimientoCantidad);
    }

    public function edit(Alerta $obj)
    {
        $this->objEntidad($obj);
        $msg = sprintf('Ingresó a editar la alerta SARLAFT \'%s\'', $obj->nombre);
        $this->log($msg);

        return view("controlVigilancia.alertasSarlaft.edit")
            ->withAlerta($obj);
    }

    public function update(Alerta $obj, EditAlertaSarlaftRequest $request)
    {
        $msg = "Actualizó la alerta SARLAFT '%s' con los siguientes parámetros %s";
        $msg = sprintf($msg, $obj->nombre, json_encode($request->all()));
        $this->log($msg, 'ACTUALIZAR');

        $modificado = false;

        //DIARIO
        if($request->diario == 1)
        {
            $obj->diario = true;
            $fecha = Carbon::now()->endOfDay();
            $obj->fecha_proximo_diario = $fecha;
            $modificado = true;
        }
        else
        {
            $obj->diario = false;
            $obj->fecha_proximo_diario = null;
            $modificado = true;
        }

        //SEMANAL
        if($request->semanal == 1)
        {
            $obj->semanal = true;
            $fecha = Carbon::now()->endOfWeek();
            $obj->fecha_proximo_semanal = $fecha;
            $modificado = true;
        }
        else
        {
            $obj->semanal = false;
            $obj->fecha_proximo_semanal = null;
            $modificado = true;
        }

        //MENSUAL
        if($request->mensual == 1)
        {
            $obj->mensual = true;
            $fecha = Carbon::now()->endOfMonth();
            $obj->fecha_proximo_mensual = $fecha;
            $modificado = true;
        }
        else
        {
            $obj->mensual = false;
            $obj->fecha_proximo_mensual = null;
            $modificado = true;
        }

        //ANUAL
        if($request->anual == 1)
        {
            $obj->anual = true;
            $fecha = Carbon::now()->endOfYear();
            $obj->fecha_proximo_anual = $fecha;
            $modificado = true;
        }
        else
        {
            $obj->anual = false;
            $obj->fecha_proximo_anual = null;
            $modificado = true;
        }

        if($modificado)
        {
            $obj->save();
        }

        Session::flash("message", "Se ha actualizado con éxito la alerta");
        return redirect("alertasSarlaft");
    }

    public static function routes()
    {
        Route::get(
            'alertasSarlaft',
            'ControlVigilancia\AlertaSarlaftController@index'
        );

        Route::get(
            'alertasSarlaft/{obj}/edit',
            'ControlVigilancia\AlertaSarlaftController@edit'
        )->name('alertasSarlaft.edit');

        Route::put(
            'alertasSarlaft/{obj}',
            'ControlVigilancia\AlertaSarlaftController@update'
        );
    }
}
