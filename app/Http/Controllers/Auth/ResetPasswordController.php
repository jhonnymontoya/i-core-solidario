<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use App\Models\Sistema\Usuario;
use App\Models\Sistema\UsuarioWeb;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Route;
use Validator;
use Session;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    const ADMINISTRADOR = 1;
    const SOCIO = 2;

    private $tipoUsuario;
    private $usuario;

    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const RESET_LINK_SENT = 'passwords.sent';

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = 'passwords.reset';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'passwords.user';

    /**
     * Constant representing an invalid password.
     *
     * @var string
     */
    const INVALID_PASSWORD = 'passwords.password';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'passwords.token';

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
        $this->middleware('throttle:15,3')->except(['showResetForm']);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request) {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $this->tipoUsuario = $this->validarUsuario($request);

        $response = $this->resetearPassword($this->credentials($request));

        return $response == ResetPasswordController::PASSWORD_RESET
                    ? $this->sendResetResponse($response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages() {
        return [
            'password.required' => 'La :attribute es requerida.'
        ];
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules() {
        return [
            'token' => 'required',
            'usuario' => 'required|string|min:3',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function resetearPassword(array $credentials) {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        $user = $this->validateReset($credentials);

        if (! is_object($user)) {
            return $user;
        }
        $this->usuario = $user;

        $password = $credentials['password'];

        // Once the reset has been validated, we'll call the given callback with the
        // new password. This gives the user an opportunity to store the password
        // in their persistent storage. Then we'll delete the token and return.
        $user->password = bcrypt($password);
        $user->save();

        $this->borrarToken($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request) {
        return $request->only('usuario', 'password', 'password_confirmation', 'token');
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials) {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->checkToken($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
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
        $credentials = Arr::except($credentials, ['token', 'password', 'password_confirmation']);
        $user = null;
        if($this->tipoUsuario == ResetPasswordController::ADMINISTRADOR)
            $user = Usuario::where($credentials)->first();
        else
            $user = UsuarioWeb::where($credentials)->first();
        return $user;
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

    private function db() {
        return DB::table(config('auth.passwords.users.table'));
    }

    private function checkToken($usuario, $token) {
        $db = $this->db();
        $registro = (array) $db->where('usuario', $usuario->usuario)->first();

        return $registro &&
               ! $this->tokenExpired($registro['created_at']) &&
                 $this->validarToken($token, $registro['token']);
    }

    private function borrarToken($usuario) {
        $db = $this->db();
        $db->where('usuario', $usuario->usuario)->delete();
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt) {
        return Carbon::parse($createdAt)->addSeconds(600)->isPast();
    }

    private function validarToken($token, $record) {
        return Hash::check($token, $record);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response) {
        return redirect()->back()
                    ->withInput($request->only('usuario'))
                    ->withErrors(['usuario' => trans($response)]);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    private function sendResetResponse($response) {
        if($this->tipoUsuario == ResetPasswordController::ADMINISTRADOR){
            return redirect($this->redirectPath());
        }
        else{
            $ruta = "/login?realm=%s";
            $entidad = $this->usuario->socios[0]->tercero->entidad;
            $entidad = $entidad->terceroEntidad->numero_identificacion;
            $ruta = sprintf($ruta, $entidad);
            return redirect($ruta);
        }
    }

    /**
     * Sirve las rutas del controlador
     * @return type
     */
    public static function routes() {
        // Password Reset Routes...
        Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    }
}
