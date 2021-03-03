<?php
namespace App\Http\Controllers\Reportes;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Reportes\ConfiguracionExtractoSocial;
use App\Http\Requests\Reportes\ConfiguracionExtractoSocial\CreateConfiguracionExtractoSocialRequest;

class ConfiguracionExtractoSocialController extends Controller
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
        $this->log("Ingresó a la configuración de extracto social");
        $configuraciones = ConfiguracionExtractoSocial::entidadId()
            ->orderBy("anio", "desc")
            ->paginate();

        return view("reportes.extractoSocial.index")
            ->withConfiguraciones($configuraciones);
    }

    public function create()
    {
        $this->log("Ingresó a crear configuración de extracto social");
        return view("reportes.extractoSocial.create");
    }

    public function store(CreateConfiguracionExtractoSocialRequest $request)
    {
        $msg = "Creó configuración de extracto social con los siguientes parámetros %s";
        $msg = sprintf($msg, json_encode($request->all()));
        $this->log($msg, "CREAR");

        $entidad = new ConfiguracionExtractoSocial();

        $entidad->fill($request->all());
        $entidad->entidad_id = $this->getEntidad()->id;

        $entidad->save();

        $msg = "Se ha guardado la configuración de extracto social para el año '%s'";
        $msg = sprintf($msg, $entidad->anio);
        Session::flash('message', $msg);
        return redirect('extractoSocial');
    }

    public function edit(ConfiguracionExtractoSocial $obj)
    {
        $msg = "Ingresó a editar la configuración de extracto social '%s'";
        $this->log(sprintf($msg, $obj->id));
        $this->objEntidad($obj);
        dd("edit");
        return view("reportes.extractoSocial.create");
    }

    public function update(ConfiguracionExtractoSocial $obj, Request $request)
    {
        $msg = "Actalizó la configuración de extracto social '%s' con los siguientes parámetros %s";
        $msg = sprintf($msg, $obj->id, json_encode($request->all()));
        $this->log($msg, "ACTUALIZAR");
        $this->objEntidad($obj);
        dd("update", $request->all());

        $obj->fill($request->all());
        $obj->save();

        $msg = "Se ha actualizado extractoSocial '%s'";
        $msg = sprintf($msg, $obj->nombre);
        Session::flash('message', $msg);
        return redirect('extractoSocial');
    }

    public static function routes()
    {
        Route::get(
            'extractoSocial',
            'Reportes\ConfiguracionExtractoSocialController@index'
        );

        Route::get(
            'extractoSocial/create',
            'Reportes\ConfiguracionExtractoSocialController@create'
        );

        Route::post(
            'extractoSocial',
            'Reportes\ConfiguracionExtractoSocialController@store'
        );

        Route::get(
            'extractoSocial/{obj}/edit',
            'Reportes\ConfiguracionExtractoSocialController@edit'
        )->name("extractoSocial.edit");

        Route::put(
            'extractoSocial/{obj}',
            'Reportes\ConfiguracionExtractoSocialController@update'
        );
    }
}
