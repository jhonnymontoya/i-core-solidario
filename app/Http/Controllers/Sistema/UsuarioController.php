<?php

namespace App\Http\Controllers\Sistema;

use DB;
use Log;
use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Models\Sistema\Perfil;
use App\Models\General\Entidad;
use App\Models\Sistema\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\General\TipoIdentificacion;
use App\Http\Requests\Sistema\Usuario\EditUsuarioRequest;
use App\Http\Requests\Sistema\Usuario\CreateUsuarioRequest;
use App\Http\Requests\Sistema\Usuario\EditUiConfiguracionRequest;

class UsuarioController extends Controller
{
    use ICoreTrait;

    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('verEnt')->except(['uiConfiguracion']);;
        $this->middleware('verMenu')->except(['uiConfiguracion']);
    }

    public function index(Request $request) {
        $entidades = Entidad::with('terceroEntidad')
            ->activa()
            ->get()
            ->pluck('terceroEntidad.razon_social', 'id');

        $usuarios = Usuario::with('perfiles')
            ->search($request->get('name'))
            ->entidad($request->get('entidad'))
            ->completo($request->get('perfil'))
            ->activo($request->get('activo'))
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->paginate();

        return view('sistema.usuario.index')
            ->withUsuarios($usuarios)
            ->withEntidades($entidades);
    }

    public function create() {
        $tipos = TipoIdentificacion::activo()
            ->aplicacion('NATURAL')
            ->orderBy('nombre')
            ->get()
            ->pluck('nombre', 'id');

        return view('sistema.usuario.create')->withTipos($tipos);
    }

    public function store(CreateUsuarioRequest $request) {
        $usuario = new Usuario;
        $usuario->fill($request->all());
        $usuario->password = bcrypt($request->password);
        $usuario->save();
        if($request->avatar) {
            $obj->imagen = $request->avatar;
            $usuario->save();
        }
        $msg = "Se ha creado el usuario '%s'";
        $msg = sprintf($msg, $usuario->nombre_corto);
        Session::flash('message', $msg);
        return redirect()->route('usuarioEdit', [$usuario->id, '#entidades']);
    }

    public function edit(Usuario $obj) {
        $entidades = Entidad::activa()
            ->with(['perfiles' => function($query){
                $query->activo()->orderBy('nombre', 'asc');
            }])
            ->get()
            ->sortBy('terceroEntidad.razon_social');

        $tipos = TipoIdentificacion::activo()
            ->aplicacion('NATURAL')
            ->orderBy('nombre')
            ->get()
            ->pluck('nombre', 'id');

        return view('sistema.usuario.edit')
            ->withTipos($tipos)
            ->withUsuario($obj)
            ->withEntidades($entidades);
    }

    public function update(EditUsuarioRequest $request, Usuario $obj) {
        $obj->fill($request->all());
        if(!empty($request->password)) {
            $obj->password = bcrypt($request->password);
        }
        if($request->avatar) {
            $obj->imagen = $request->avatar;
        }
        $obj->save();
        $obj->perfiles()->detach();
        if($request->has('entidades')) {
            $entidades = $request->entidades;
            foreach($entidades as $entidad) {
                $perfil = Perfil::find($entidad);
                if($perfil != null)$obj->perfiles()->attach($perfil);
            }
        }
        $msg = "Se ha actualizado el usuario '%s'";
        $msg = sprintf($msg, $obj->nombre_corto);
        Session::flash('message', $msg);
        return redirect('usuario');
    }

    public function show(Usuario $obj) {
        return view('sistema.usuario.show')->withUsuario($obj);
    }

    public function uiConfiguracion(EditUiConfiguracionRequest $request) {
        $request = json_encode($request->only("clase", "tema"));
        $usuario = $this->getUser();
        $usuario->ui_configuracion = $request;
        $usuario->save();
        return request()->json("");
    }

    public static function routes() {
        Route::get('usuario', 'Sistema\UsuarioController@index');
        Route::get('usuario/create', 'Sistema\UsuarioController@create');
        Route::post('usuario', 'Sistema\UsuarioController@store');
        Route::get(
            'usuario/uiConfiguracion',
            'Sistema\UsuarioController@uiConfiguracion'
        )->name('usuario.configuracion');
        Route::get('usuario/{obj}/edit', 'Sistema\UsuarioController@edit')
            ->name('usuarioEdit');
        Route::put('usuario/{obj}', 'Sistema\UsuarioController@update')
            ->name('usuarioShow');
        Route::get('usuario/{obj}', 'Sistema\UsuarioController@show');
    }
}
