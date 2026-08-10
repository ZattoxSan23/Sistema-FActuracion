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
    ->withMiddleware(function (Middleware $middleware) {
        // Alias de middleware personalizados
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'caja.abierta' => \App\Http\Middleware\CajaAbierta::class,
        ]);

        // Excluir rutas de la verificación CSRF si es necesario
        $middleware->validateCsrfTokens(except: [
            'sunat/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
