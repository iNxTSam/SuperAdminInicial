<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol_id' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'no-cache' => \App\Http\Middleware\NoCacheMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            // **Reemplaza 'tu/ruta/creacion' con la URI exacta de tu endpoint POST**
            // Por ejemplo: 'register', 'crear-usuario', etc.
            'http://127.0.0.1:8000/usuarios', 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
