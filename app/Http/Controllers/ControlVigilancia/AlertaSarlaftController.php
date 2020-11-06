<?php

namespace App\Http\Controllers\ControlVigilancia;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ControlVigilancia\Alerta;

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
            ->orderBy("nombre")
            ->get();dd($alertas);

        return view("controlVigilancia.alertasSarlaft.index")
            ->withAlertas($alertas);
    }

    public function edit(Alerta $obj)
    {
        $msg = sprintf('Ingresó a editar la alerta SARLAFT \'\'', $obj->nombre);
        $this->log($msg);

        return view("controlVigilancia.alertasSarlaft.edit")
            ->withAlerta($obj);
    }

    public function update(EditOficialCumplimientoRequest $request)
    {
        $this->log("Creó el oficial de cumplimiento con los siguientes parámetros " . json_encode($request->all()), 'ACTUALIZAR');
        $alertas = OficialCumplimiento::entidadId()
            ->activo()
            ->orderBy("id", "desc")
            ->first();

        if($alertas == null) {
            Session::flash("error", "No existe ningún oficial de cumplimiento");
            return redirect("alertasSarlaft");
        }

        $alertas->delete();

        $entidadId = $this->getEntidad()->id;
        $alertas = new OficialCumplimiento;
        $alertas->fill($request->all());
        $alertas->entidad_id = $entidadId;
        $alertas->email_copia = $request->emailcc;
        $alertas->save();

        Session::flash("message", "Se ha actualizado con éxito el oficial de cumplimiento");
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
        );

        Route::put(
            'alertasSarlaft',
            'ControlVigilancia\AlertaSarlaftController@update'
        );
    }
}
