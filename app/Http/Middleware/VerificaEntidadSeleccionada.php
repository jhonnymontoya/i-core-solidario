<?php

namespace App\Http\Middleware;

use Closure;

class VerificaEntidadSeleccionada
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
		if(!$request->session()->has('entidad'))
		{
			return redirect('entidad/seleccion');
		}
		return $next($request);
	}
}
