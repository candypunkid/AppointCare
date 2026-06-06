<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Append to web group
        $middleware->web(append: [
            \App\Http\Middleware\ResolveTenant::class,
        ]);

        // Exclude Twilio webhooks from CSRF
        $middleware->validateCsrfTokens(except: [
            'twilio/*',
        ]);

        // Custom middleware aliases
        $middleware->alias([
            'tenant'                   => \App\Http\Middleware\EnsureTenantAccess::class,
            'role'                     => \App\Http\Middleware\RoleMiddleware::class,
            'validate.twilio.webhook'  => \Modules\Twilio\Http\Middleware\ValidateTwilioWebhook::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
