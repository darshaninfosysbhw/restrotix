<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CountryManager::class,
            \App\Http\Middleware\SetUserCurrency::class,

        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleManager::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'check.service' => \App\Http\Middleware\CheckServiceAddon::class,
        ]);

        $middleware->redirectUsersTo(function () {
            $user = Auth::user();

            if ($user) {
                if ($user->role === 'superadmin') {
                    return route('superadmin.dashboard');
                }

                if (in_array($user->role, ['admin', 'manager', 'sales_manager', 'purchase_manager', 'account_manager', 'chef'])) {
                    return route('admin.dashboard');
                }
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
