<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use Route;
use Validator;
use Carbon\Carbon;
use App\Traits\ICoreTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Sistema\Modulo;
use App\Models\Sistema\UsuarioWeb;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Support\Facades\Hash;
use App\Mail\Sistema\PasswordResetLink;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;

class LoginController extends Controller
{
    use ICoreTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'sendResetLinkEmail']);
    }

    public function login(LoginRequest $request)
    {
        $entidadId = $this->getEntidadIdParaApi($request->usuario);
        $estaAppMovilActiva = $this->validarModuloAppActivo($request->usuario);
        if($estaAppMovilActiva == false){
            $msg = "API: Intentó ingresar al sistema con APP Movil desabilitada '%s'";
            $msg = sprintf($msg, $request->usuario);
            $this->log($msg, 'INGRESAR', $entidadId);
            return response()->json(['message' => 'App Móvil no activa'], 412);
        }

        if(!Auth::attempt(request(['usuario', 'password']))) {
            $msg = "API: Intentó ingresar al sistema con contraseña errada '%s'";
            $msg = sprintf($msg, $request->usuario);
            $this->log($msg, 'INGRESAR', $entidadId);
            return response()->json(['message' => 'No autorizado'], 401);
        }
        $usuario = $request->user();
        $tokenRes = $usuario->createToken('I-Core Token');
        $token = $tokenRes->token;
        $token->save();

        $msg = "API: Ingresó al sistema: '%s'";
        $msg = sprintf($msg, $request->usuario);
        $this->log($msg, 'INGRESAR', $entidadId);
        return response()->json([
            'token' => $tokenRes->accessToken,
            'fechaExpiracion' => $token->expires_at
        ]);
    }

    public function validarCredenciales(LoginRequest $request)
    {
        $entidadId = $this->getEntidadIdParaApi($request->usuario);
        $credenciales = request(['usuario', 'password']);

        $respuesta = false;
        $usuario = $request->user();
        $respuesta = $usuario->usuario == $credenciales["usuario"]
            && Hash::check($credenciales["password"], $usuario->password);
        if(!$respuesta) {
            $msg = "API: Intentó validar credenciales sin éxito '%s'";
            $msg = sprintf($msg, $request->usuario);
            $this->log($msg, 'CONSULTAR', $entidadId);
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $msg = "API: Validó credenciales para Biometrico: '%s'";
        $msg = sprintf($msg, $request->usuario);
        $this->log($msg, 'CONSULTAR', $entidadId);
        return response()->json();
    }

    public function logout(Request $request)
    {
        $usuario = $request->user();
        $entidadId = $this->getEntidadIdParaApi($usuario->usuario);
        $msg = "API: Salió del sistema: '%s'";
        $msg = sprintf($msg, $usuario->usuario);
        $this->log($msg, 'SALIR', $entidadId);
        $request->user()->token()->revoke();
    }

    /**
     * Envia correo electronico con link de recuperación de contraseña
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $entidadId = $this->getEntidadIdParaApi($request->usuario);

        $msg = "API: Envió link de restauración de contraseña: '%s'";
        $msg = sprintf($msg, $request->usuario);
        $this->log($msg, 'INGRESAR', $entidadId);
        //Se busca el usuario
        $user = $this->getUser($request->only("usuario"));

        //Se crea token de recuperación de contraseña
        $token = $this->crearToken($user);

        //Se envia el correo con el token generado como url
        $correos = $this->obtenerCorreos($user);
        foreach ($correos as $correo) {
            Mail::to($correo)->send(new PasswordResetLink($token));
        }
    }

    public function cambiarPassword(ResetPasswordRequest $request)
    {
        $credenciales = request(['usuario', 'passwordActual', 'password']);

        $respuesta = false;
        $usuario = $request->user();
        $entidadId = $this->getEntidadIdParaApi($usuario->usuario);
        $respuesta = $usuario->usuario == $credenciales["usuario"]
            && Hash::check($credenciales["passwordActual"], $usuario->password);
        if(!$respuesta) {
            $msg = "API: Intentó cambiar la contraseña sin exito '%s'";
            $msg = sprintf($msg, $request->usuario);
            $this->log($msg, 'ACTUALIZAR', $entidadId);
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $usuario->password = bcrypt($credenciales["password"]);
        $usuario->save();

        $msg = "API: Actualizó la contraseña: '%s'";
        $msg = sprintf($msg, $request->usuario);
        $this->log($msg, 'ACTUALIZAR', $entidadId);
        return response()->json();
    }

    public function ping(Request $request)
    {
        $usuario = $request->user();
        $entidadId = $this->getEntidadIdParaApi($usuario->usuario);
        $msg = "API: Ping de token de seguridad: '%s'";
        $msg = sprintf($msg, $usuario->usuario);
        $this->log($msg, 'CONSULTAR', $entidadId);
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     *
     * @throws \UnexpectedValueException
     */
    private function getUser(array $credentials) {
        $credentials = Arr::except($credentials, ['token']);
        $user = UsuarioWeb::where($credentials)->first();
        return $user;
    }

    private function crearToken($usuario) {
        $db = $this->db();

        //se busca si el usuario ya tiene un token de recuperación de contraseña
        //si lo tiene, se elimina
        $db->where('usuario', $usuario->usuario)->delete();

        //Se crea nuevo token
        $token = Str::random(64);

        $db->insert([
            'usuario' => $usuario->usuario,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);
        return $token;
    }

    private function db() {
        return DB::table(config('auth.passwords.users.table'));
    }

    private function obtenerCorreos($user) {
        $correos = collect();
        foreach($user->socios as $socio) {
            $tercero = $socio->tercero;
            $contactos = $tercero->contactos()->whereNotNull("email")->get();
            foreach ($contactos as $contacto) {
                $val = Validator::make(
                    ["email" => $contacto->email],
                    ["email" => "bail|required|email:rfc,dns"]
                );
                if(!$val->fails())
                    $correos->push($contacto->email);
            }
        }
        return $correos;
    }

    protected function validarModuloAppActivo($socio)
    {
        $entidad_id = $this->getEntidadIdParaApi($socio);
        $modulo = Modulo::entidadId($entidad_id)->codigo('APPMOVIL')->first();
        if(is_null($modulo) == true) return false;
        return $modulo->esta_activo;
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::post('1.0/login', 'Api\Auth\LoginController@login');
        Route::post('1.0/validarCredenciales', 'Api\Auth\LoginController@validarCredenciales');
        Route::get('1.0/logout', 'Api\Auth\LoginController@logout');
        Route::post('1.0/forgotPassword', 'Api\Auth\LoginController@sendResetLinkEmail');
        Route::post('1.0/cambiarPassword', 'Api\Auth\LoginController@cambiarPassword');
        Route::post('1.0/ping', 'Api\Auth\LoginController@ping');
    }
}
