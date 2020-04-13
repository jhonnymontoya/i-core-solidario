<?php

namespace App\Http\Middleware;

use Closure;

class AlwaysJson
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
        $request->server->set('HTTP_ACCEPT', 'application/json');
        $request->headers->set('accept', 'application/json');
        $res = $next($request);
        return $res;
    }
}
