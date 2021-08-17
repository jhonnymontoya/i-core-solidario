<?php

namespace App\Http\Controllers\Sistema;

use DB;
use Log;
use Auth;
use Route;
use App\Models\Sistema\Menu;
use Illuminate\Http\Request;
use App\Models\Sistema\Perfil;
use App\Models\General\Entidad;
use App\Models\Sistema\MenuPerfil;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Sistema\Perfil\EditPerfilRequest;
use App\Http\Requests\Sistema\Perfil\CreatePerfilRequest;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request)
    {
        $this->logActividad("Ingreso a perfiles", $request);

        $entidades = Entidad::with('terceroEntidad')->get();
        $listaEntidades = array();
        foreach($entidades as $entidad)
            $listaEntidades[$entidad->id] = $entidad->terceroEntidad->nombre_corto;

        $perfiles = Perfil::with(['entidad', 'menus'])
            ->entidadId($request->entidad)
            ->search($request->name)
            ->activo($request->estado);

        if($this->esSesionRoot() == false){
            $usuarios->whereEsRoot(false);
        }

        $perfiles = $perfiles->paginate();

        return view('sistema.perfil.index')
            ->withPerfiles($perfiles)
            ->withEntidades($listaEntidades);
    }

    public function create()
    {
        $this->log("Ingresó a crear nuevo perfil");
        $entidades = Entidad::activa()
            ->get()
            ->sortBy('terceroEntidad.razon_social')
            ->pluck('terceroEntidad.razon_social', 'id');

        $menus = Menu::listarTodos();

        return view('sistema.perfil.create')
            ->withMenuList($menus)
            ->withEntidades($entidades);
    }

    public function store(CreatePerfilRequest $request)
    {
        $msg = sprintf("Creó un perfil '%s'", $request->nombre);
        $this->log($msg, "CREAR");

        $perfil = new Perfil;
        try {
            DB::transaction(function() use($perfil, $request){
                $perfil = Entidad::find($request->entidad_id)
                    ->perfiles()
                    ->create($request->all());

                $perfil->menus()
                    ->attach(Menu::find($request->menus));
            });
        }
        catch(Exception $e) {
            Log::error('Error creando el perfil: ' . $e->getMessage());
            abort(500, 'Error al crear el perfil');
        }
        Session::flash('message', 'Se ha creado un nuevo perfil');
        return redirect('perfil');
    }

    public function edit(Perfil $obj)
    {
        if($obj->es_root && $this->esSesionRoot() == false)
        {
            $msg = "Intentó ingresar a editar el perfil '%s' sin permiso";
            $this->log(sprintf($msg, $obj->nombre));
            abort(404);
        }

        $msg = "Ingresó a editar el perfil '%s'";
        $this->log(sprintf($msg, $obj->nombre));

        $entidades = Entidad::activa()
            ->get()
            ->sortBy('terceroEntidad.razon_social')
            ->pluck('terceroEntidad.razon_social', 'id');

        $menus = Menu::listarTodos();
        return view('sistema.perfil.edit')
            ->withMenuList($menus)
            ->withPerfil($obj)
            ->withEntidades($entidades);
    }

    public function update(EditPerfilRequest $request, Perfil $obj)
    {
        if($obj->es_root && $this->esSesionRoot() == false)
        {
            $msg = "Intentó actualizar el perfil '%s' sin permiso";
            $this->log(sprintf($msg, $obj->nombre));
            abort(404);
        }

        $msg = sprintf("Actualizó el perfil '%s'", $obj->nombre);
        $this->log($msg, "ACTUALIZAR");

        $obj->fill($request->all());
        try {
            DB::transaction(function() use($obj, $request){
                $obj->save();
                $obj->menus()->detach();
                $obj->menus()->attach(Menu::find($request->menus));
            });
        }
        catch(Exception $e) {
            Log::error('Error actualizando el perfil: ' . $e->getMessage());
            abort(500, 'Error al actualizar el perfil');
        }
        Session::flash('message', 'Se ha actualizado el perfil');
        return redirect('perfil');
    }

    private function esSesionRoot()
    {
        return Auth::user()->es_root;
    }

    public static function routes()
    {
        Route::get('perfil', 'Sistema\PerfilController@index');
        Route::get('perfil/create', 'Sistema\PerfilController@create');
        Route::post('perfil', 'Sistema\PerfilController@store');
        Route::get('perfil/{obj}/edit', 'Sistema\PerfilController@edit')
            ->name('perfilEdit');
        Route::put('perfil/{obj}', 'Sistema\PerfilController@update');
    }
}
