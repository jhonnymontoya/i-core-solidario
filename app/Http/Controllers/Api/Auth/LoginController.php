<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use Route;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ICoreTrait;
use App\Models\Sistema\UsuarioWeb;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Support\Facades\Hash;
use App\Mail\Sistema\PasswordResetLink;
use App\Http\Requests\Api\Auth\LoginRequest;
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
        $credenciales = request(['usuario', 'password']);
        $activo = $this->validarModuloAppActivo($credenciales->usuario);
        if($activo == false){
            $this->log("API: Intentó de ingreso al sistema con APP Movil desabilitada" . $request->usuario, 'INGRESAR');
            return response()->json(['message' => 'App Movil no activa'], 412);
        }

        if(!Auth::attempt($credenciales)) {
            $this->log("API: Intentó de ingreso al sistema " . $request->usuario, 'INGRESAR');
            return response()->json(['message' => 'No autorizado'], 401);
        }
        $usuario = $request->user();
        $tokenRes = $usuario->createToken('I-Core Token');
        $token = $tokenRes->token;
        $token->save();
        $this->log("API: Ingresó al sistema: " . $request->usuario, 'INGRESAR');
        return response()->json([
            'token' => $tokenRes->accessToken,
            'fechaExpiracion' => $token->expires_at
        ]);
    }

    public function logout(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Salió del sistema: " . $usuario->usuario, 'SALIR');
        $request->user()->token()->revoke();
    }

    /**
     * Envia correo electronico con link de recuperación de contraseña
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $this->log("API: Envió link de restauración de contraseña: " . $request->usuario, 'INGRESAR');
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

    public function ping(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Ping de token de seguridad: " . $usuario->usuario, 'INGRESAR');
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
                $val = Validator::make(["email" => $contacto->email], ["email" => "bail|required|email"]);
                if(!$val->fails())
                    $correos->push($contacto->email);
            }
        }
        return $correos;
    }

    protected function validarModuloAppActivo($socio)
    {
        $socio = UsuarioWeb::with([
            'socios',
            'socios.tercero'
        ])->whereUsuario($socio)->first();
        $entidad_id = $socio->socios[0]->tercero->entidad_id;
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
        Route::get('1.0/logout', 'Api\Auth\LoginController@logout');
        Route::post('1.0/forgotPassword', 'Api\Auth\LoginController@sendResetLinkEmail');
        Route::post('1.0/ping', 'Api\Auth\LoginController@ping');
    }
}
