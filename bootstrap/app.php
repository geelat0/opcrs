<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\Handle419Error;
use App\Http\Middleware\NotAuthenticated;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\Verify2FA;
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

        $middleware->appendToGroup('guest', [
            RedirectIfAuthenticated::class,
        ]);

        $middleware->appendToGroup('auth_check', [
            NotAuthenticated::class,
        ]);
        $middleware->appendToGroup('2fa', [
            Verify2FA::class,
        ]);

        $middleware->appendToGroup('superadmin', [
            SuperAdminMiddleware::class
        ]);

        $middleware->appendToGroup('419', [
            Handle419Error::class
        ]);

        $middleware->alias([
            'permission' => CheckPermission::class
        ]);



    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
