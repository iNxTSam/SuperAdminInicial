<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->rol_id == 1) { // ID=1 = Super Admin
            return $next($request);
        }

        return redirect('/')->with('error', 'No tienes permisos de Super Administrador.');
    }
}
