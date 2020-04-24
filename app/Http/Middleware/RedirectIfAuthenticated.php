<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
{
    const ADMINISTRADOR = 1;
    const SOCIO = 2;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if(Session::has("tipoUsuario")) {
                if(Session::get("tipoUsuario") == RedirectIfAuthenticated::ADMINISTRADOR) {
                    return redirect('/dashboard');
                }
                elseif(Session::get("tipoUsuario") == RedirectIfAuthenticated::SOCIO) {
                    return redirect('/consulta');
                }
                else {
                    Auth::guard($guard)->logout();
                    Session::invalidate();
                    return redirect('login');
                }
            }
            else {
                Auth::guard($guard)->logout();
                Session::invalidate();
                return redirect('login');
            }
        }

        return $next($request);
    }
}
