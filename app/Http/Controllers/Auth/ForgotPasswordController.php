<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Sistema\PasswordResetLink;
use App\Models\Sistema\Usuario;
use App\Models\Sistema\UsuarioWeb;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Route;
use Validator;
use \Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    const ADMINISTRADOR = 1;
    const SOCIO = 2;

    private $tipoUsuario;
    private $correos = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
        $this->middleware('throttle:15,3')->except(['showLinkRequestForm']);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request) {
        $tipoUsuario = $this->validarUsuario($request);

        if(!$tipoUsuario) {
            return redirect()->back()->withInput()->withErrors(['usuario' => ['Usuario inactivo o inexistente']]);
        }

        $this->tipoUsuario = $tipoUsuario;

        $response = $this->sendResetLink($request->only('usuario'));
        return $this->sendResetLinkResponse($response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse($response) {
        return back()
            ->with('status', trans($response))
            ->with('correos', $this->correos);
    }

    public function validarUsuario(Request $request) {
        $this->validate($request, ['usuario' => 'required|string|min:3']);
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

    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendResetLink(array $credentials) {
        //Se busca el usuario
        $user = $this->getUser($credentials);

        //Se crea token de recuperación de contraseña
        $token = $this->crearToken($user);

        //Se envia el correo con el token generado como url
        if($this->tipoUsuario == ForgotPasswordController::ADMINISTRADOR) {
            Mail::to($user->email)->send(new PasswordResetLink($token));
            $correos = collect();
            $correos->push($this->enmascararCorreo($user->email));
            $this->correos = $correos;
        }
        else {
            $correos = $this->obtenerCorreos($user);
            foreach ($correos as $correo) {
                Mail::to($correo)->send(new PasswordResetLink($token));
            }
            $this->correos = $this->enmascararCorreos($correos);
        }

        //se retorna ok
        return 'passwords.sent';
    }

    private function obtenerCorreos($user) {
        $correos = collect();
        foreach($user->socios as $socio) {
            $tercero = $socio->tercero;
            $contactos = $tercero->contactos()->whereNotNull("email")->get();
            foreach ($contactos as $contacto) {
                $val = Validator::make(["email" => $contacto->email], ["email" => "bail|required|email"]);
                if(!$val->fails())
                    $correos->push($contacto->email);
            }
        }
        return $correos;
    }

    private function enmascararCorreos($correos) {
        $correosEnmascarados = collect();
        foreach ($correos as $correo)
            $correosEnmascarados->push($this->enmascararCorreo($correo));
        return $correosEnmascarados;
    }

    public function enmascararCorreo($correo) {
        list($nombre, $dominio) = explode("@", $correo);
        $nombre = substr($nombre, 0, 2) . str_pad("", strlen($nombre) - 2, "*");
        $dominio = substr($dominio, 0, 2) . str_pad("", strlen($dominio) - 2, "*");
        $correo = str_limit($nombre . "@" . $dominio, 40);
        return $correo;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(array $credentials) {
        $credentials = Arr::except($credentials, ['token']);
        if($this->tipoUsuario == ForgotPasswordController::ADMINISTRADOR) {
            $user = Usuario::where($credentials)->first();
        }
        else {
            $user = UsuarioWeb::where($credentials)->first();
        }
        return $user;
    }

    private function db() {
        return DB::table(config('auth.passwords.users.table'));
    }

    private function crearToken($usuario) {
        $db = $this->db();

        //se busca si el usuario ya tiene un token de recuperación de contraseña
        //si lo tiene, se elimina
        $db->where('usuario', $usuario->usuario)->delete();

        //Se crea nuevo token
        $token = str_random(64);

        $db->insert([
            'usuario' => $usuario->usuario,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);
        return $token;
    }

    /**
     * Sirve las rutas del controlador
     * @return type
     */
    public static function routes() {
        // Password Reset Routes...
        Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    }
}
