<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->api(prepend: [\Illuminate\Http\Middleware\HandleCors::class]);
        $middleware->validateCsrfTokens(except: [
            'admin/login',
            'employee/login',
            'admin/logout',
            'employee/logout',
        ]);
        $middleware->alias([
            'admin.auth'      => \App\Http\Middleware\AdminAuth::class,
            'employee.auth'   => \App\Http\Middleware\EmployeeAuth::class,
            'technician.auth' => \App\Http\Middleware\TechnicianApiAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
