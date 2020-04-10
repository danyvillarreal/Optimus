<?php

namespace App\Http\Middleware;

use Closure;

class UserMiddleware
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
        if (Auth::user()->hasRole('admin')) {
            return new Response(view('velcome')->with('role', 'admin'));
        }
        // if ($request->user() && $request->user()->type != 'admin'){
        //     return new Response(view('unauthorized')->with('role', 'admin'));
        // }
        return $next($request);
    }
}
