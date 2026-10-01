<?php

use App\Http\Middleware\EnsureTwoFactor;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\SecurityHeaders;
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
        // The app runs behind the host's HTTPS proxy; trust it so secure cookies,
        // HSTS and the real client IP (rate limits, audit log) work.
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [SecurityHeaders::class]);

        $middleware->alias([
            'twofactor' => EnsureTwoFactor::class,
            'role' => RequireRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
