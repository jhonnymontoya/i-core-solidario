<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\General\Tercero;
use App\Models\Sistema\Usuario;
use App\Models\Sistema\UsuarioWeb;
use App\Traits\FonadminTrait;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Route;
use Session;
use Validator;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers, FonadminTrait;

	const ADMINISTRADOR = 1;
	const SOCIO = 2;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/dashboard';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('guest')->except('logout');
		$this->middleware('throttle:15,3')->except(['logout', 'showLoginForm']);
	}

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm(Request $request) {
		if($request->has('volver')) Session::forget('usuario');
		$this->realm($request);
		if(!Session::has('usuario')) return view('auth.login');
		return view('auth.password');
	}

	private function realm($request) {
		$realmAvatar = "img/logos/icore.png";
		$realm = $request->validate([
			'realm' => [
				'bail',
				'nullable',
				'string',
				'digits:9',
				'exists:sqlsrv.general.terceros,numero_identificacion,tipo_tercero,JURIDICA,tipo_identificacion_id,2,deleted_at,NULL'
			]
		]);
		if(isset($realm["realm"])) {
			$realmId = $realm["realm"];
			$realms = Tercero::where('tipo_tercero', 'JURIDICA')->where('numero_identificacion', $realmId)->get();
			$realm = null;
			foreach($realms as $r) {
				if($r->entidad->terceroEntidad->numero_identificacion == $realmId) {
					Session::put("realm", $r->entidad);
					if(isset($r->entidad->categoriaImagenes[1])) {
						$realmAvatar = $r->entidad->categoriaImagenes[1]->pivot->nombre;
						$realmAvatar = "storage/entidad/" . $realmAvatar;
					}
					break;
				}
			}
		}
		Session::put("realmAvatar", $realmAvatar);
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	 */
	public function login(Request $request) {
		$this->validateLogin($request);

		if(Session::has("usuario") == false) {
			$tipoUsuario = $this->validarUsuario($request);

			if(is_null($tipoUsuario)) {
				$mensaje = ['usuario' => ['Usuario no existe o inactivo']];
				return redirect()->back()->withInput()->withErrors($mensaje);
			}
			$avatar = $this->obtenerAvatar($request->usuario, $tipoUsuario);
			Session::put("usuario", $request->usuario);
			Session::put("tipoUsuario", $tipoUsuario);
			Session::put("avatar", $avatar);
			return redirect()->route('login');
		}

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);
			return $this->sendLockoutResponse($request);
		}

		if ($this->attemptLogin($request)) {
			$this->log("Ingresó al sistema: " . $request->usuario, 'INGRESAR');
			if(Session::get("tipoUsuario") == LoginController::SOCIO) {
				$entidad = \Auth::user()->socios[0]->tercero->entidad;
				Session::put('entidad', $entidad);
			}
			return $this->sendLoginResponse($request);
		}
		$this->log("Intento de ingreso al sistema: " . Session::get('usuario'), 'INGRESAR');

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	/**
	 * Send the response after the user was authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	protected function sendLoginResponse(Request $request) {
		$request->session()->regenerate();
		$this->clearLoginAttempts($request);
		return $this->authenticated($request, $this->guard()->user()) ?: redirect($this->redirectTo);
	}

	public function validarUsuario(Request $request) {
		$val = Validator::make($request->only('usuario'), [
			'usuario' => 'exists:sqlsrv.sistema.usuarios,usuario,esta_activo,1,deleted_at,NULL'
		]);

		//Se verifica si el usuario es administrador
		if(!$val->fails()) {
			return LoginController::ADMINISTRADOR;
		}

		$val = Validator::make($request->only('usuario'), [
			'usuario' => 'exists:sqlsrv.sistema.usuarios_web,usuario,esta_activo,1,deleted_at,NULL'
		]);

		//Se verifica si el usuario es socio
		if(!$val->fails()) {
			return LoginController::SOCIO;
		}

		//De lo contrario se retorna null
		return null;
	}

	private function obtenerAvatar($usuario, $tipoUsuario) {
		$avatarPath = "storage/asociados/";
		$avatar = "avatar-160x160.png";

		if($tipoUsuario == LoginController::ADMINISTRADOR) {
			$usuario = Usuario::activo(true)->whereUsuario($usuario)->first();
			$avatarPath = "storage/avatars/";
			$avatar = $usuario->avatar;
		}

		if($tipoUsuario == LoginController::SOCIO) {
			$usuario = UsuarioWeb::activo()->whereUsuario($usuario)->first();
			$usuario = $usuario->socios->first();
			if($usuario && strlen($usuario->avatar) > 0) {
				$avatar = $usuario->avatar;
			}
		}

		return sprintf("%s%s", $avatarPath, $avatar);
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function attemptLogin(Request $request) {
		if (Session::get('tipoUsuario') == LoginController::ADMINISTRADOR) {
			$guard = Auth::guard('admin');
			$this->redirectTo = '/dashboard';
		}
		else {
			$guard = Auth::guard('web');
			$this->redirectTo = '/consulta';
		}
		return $guard->attempt($this->credentials($request), $request->filled('remember'));
	}

	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function credentials(Request $request) {
		return [
			$this->username() => Session::get('usuario'),
			'password' => $request->password
		];
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username() {
		return 'usuario';
	}

	/**
	 * Validate the user login request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	protected function validateLogin(Request $request) {
		if(!Session::has("usuario")) {
			$this->validate($request, [
				$this->username() => 'required|string',
			]);
		}else {
			$this->validate($request, [
				'password' => 'required|string',
			],['password.required' => 'La contraseña es requerida']);
		}
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request) {
		$ruta = "/";
		if(Session::has("tipoUsuario") && Session::get("tipoUsuario") == LoginController::SOCIO) {
			$entidad = Session::get("entidad");
			$entidad = $entidad->terceroEntidad->numero_identificacion;
			$ruta = url('login') . "?realm=" . $entidad;
		}
		$this->guard()->logout();

		$request->session()->invalidate();

		return $this->loggedOut($request) ?: redirect($ruta);
	}

	/**
	 * Get the failed login response instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function sendFailedLoginResponse(Request $request) {
		throw ValidationException::withMessages([
			'password' => [trans('auth.failed')],
		]);
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  mixed  $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user) {
		auth()->logoutOtherDevices($request->password);
	}

	/**
	 * Sirve las rutas del controlador
	 * @return type
	 */
	public static function routes() {
		// Authentication Routes...
		Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
		Route::post('login', 'Auth\LoginController@login');
		Route::post('logout', 'Auth\LoginController@logout')->name('logout');
	}
}
