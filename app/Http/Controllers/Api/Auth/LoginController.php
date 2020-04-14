<?php

namespace App\Http\Controllers\Api\Auth;

use Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\FonadminTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;

class LoginController extends Controller
{
    use FonadminTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['login']);
    }

    public function login(LoginRequest $request)
    {
        $credenciales = request(['usuario', 'password']);
        if(!Auth::attempt($credenciales)) {
            $this->log("API: Intento de ingreso al sistema " . $request->usuario, 'INGRESAR');
            return response()->json(['message' => 'No autorizado'], 401);
        }
        $usuaio = $request->user();
        $tokenRes = $usuaio->createToken('I-Core Token');
        $token = $tokenRes->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $this->log("API: Ingresó al sistema: " . $request->usuario, 'INGRESAR');
        return response()->json([
            'access_token' => $tokenRes->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $token->expires_at->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        $usuario = $request->user();
        $this->log("API: Salió del sistema: " . $usuario->usuario, 'SALIR');
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Salida exitosa']);
    }

    /**
     * Establece las rutas
     */
    public static function routes()
    {
        Route::post('1.0/login', 'Api\Auth\LoginController@login');
        Route::get('1.0/logout', 'Api\Auth\LoginController@logout');
    }
}
