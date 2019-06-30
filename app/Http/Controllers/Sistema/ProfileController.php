<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sistema\Profile\EditProfileRequest;
use App\Mail\Sistema\Profile\PasswordUpdated;
use App\Models\General\TipoIdentificacion;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Image;
use Route;

class ProfileController extends Controller
{
	public function __construct() {
		$this->middleware('auth:admin');
	}

	/**
	 * Muestra el perfil del usuario
	 * @return type
	 */
	public function index() {
		return view('sistema.profile.index')->withUsuario(Auth::user());
	}

	/**
	 * Muestra el formulario para editar el usuario
	 * @return type
	 */
	public function edit() {
		$tipos = TipoIdentificacion::activo()->aplicacion('NATURAL')->orderBy('nombre')->get()->pluck('nombre', 'id');
		return view('sistema.profile.edit')->withTipos($tipos)->withUsuario(Auth::user());
	}

	/**
	 * Guarda los datos del usuario
	 * @return type
	 */
	public function update(EditProfileRequest $request) {
		$usuario = Auth::user();
		$tipoIdentificacion = TipoIdentificacion::find($request->tipo_identificacion_id);
		$usuario->tipoIdentificacion()->dissociate();
		$usuario->tipoIdentificacion()->associate($tipoIdentificacion);

		$usuario->identificacion		= $request->identificacion;
		$usuario->primer_nombre			= $request->primer_nombre;
		$usuario->segundo_nombre		= $request->segundo_nombre;
		$usuario->primer_apellido		= $request->primer_apellido;
		$usuario->segundo_apellido		= $request->segundo_apellido;
		$usuario->email					= $request->email;
		
		if(!empty($request->password)) {
			$usuario->password			= bcrypt($request->password);
		}

		if(!empty($request->avatar)) {
			$usuario->imagen			= $request->avatar;
		}
		$usuario->save();

		if(!empty($request->password)) {
			Mail::to($usuario->email)->send(new PasswordUpdated($usuario));
		}
		Session::flash('message', 'Se ha actualizado el perfil');
		return redirect('profile');
	}

	public static function routes() {
		Route::get('profile', 'Sistema\ProfileController@index');
		Route::get('profile/edit', 'Sistema\ProfileController@edit');
		Route::put('profile', 'Sistema\ProfileController@update');
	}
}
