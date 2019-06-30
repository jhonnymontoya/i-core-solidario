<?php

namespace App\Http\Middleware;

use Closure;

use App\Helpers\MenuHelper;

class VerificaMenuSeleccionado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $menus = new MenuHelper;
        if(!$menus->menuEsPermitido())
        {
            return redirect('dashboard');
        }
        return $next($request);
    }
}
