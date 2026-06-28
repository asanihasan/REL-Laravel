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
        // Your existing CSRF configuration
        $middleware->validateCsrfTokens(except: [
            'telegram/webhook',
        ]);

        // Add your custom permission alias here
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckGroupPermission::class,
            'internal.only' => \App\Http\Middleware\RestrictToInternalNetwork::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();