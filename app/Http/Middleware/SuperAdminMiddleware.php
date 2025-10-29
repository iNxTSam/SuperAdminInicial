<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next, int $rol_id):Response
    {   
        if(!Auth::check()|| Auth::user()->rol_id != $rol_id){
            abort(403, 'Acceso no autorizado');
        }
        return $next($request);
    }
}
