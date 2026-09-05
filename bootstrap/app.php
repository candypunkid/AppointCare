<?php

use App\Http\Middleware\EnsureTenantAccess;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\RoleMiddleware;
use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Modules\Twilio\Http\Middleware\ValidateTwilioWebhook;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {

        // Append to web group
        $middleware->web(append: [
            ResolveTenant::class,
        ]);

        // Trust ngrok's forwarded headers so route() generates https:// webhook URLs.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        // Exclude Twilio webhooks from CSRF
        $middleware->validateCsrfTokens(except: [
            'twilio/*',
        ]);

        // Custom middleware aliases
        $middleware->alias([
            'tenant' => EnsureTenantAccess::class,
            'role' => RoleMiddleware::class,
            'validate.twilio.webhook' => ValidateTwilioWebhook::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
