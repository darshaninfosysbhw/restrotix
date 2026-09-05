<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\ForbiddenRedirector;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            \App\Http\Middleware\SetBranchContext::class,

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
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return app(ForbiddenRedirector::class)->handle($request, $e->getMessage() ?: null);
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            $status = (int) $e->getStatusCode();

            if ($status === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired. Please login again.',
                    ], 419);
                }

                return redirect()
                    ->route('login')
                    ->with('toast', [
                        [
                            'type' => 'warning',
                            'message' => 'Your session has expired. Please login again.',
                            'duration' => 6000,
                        ],
                    ]);
            }

            if ($status !== 403) {
                return null;
            }

            return app(ForbiddenRedirector::class)->handle($request, $e->getMessage() ?: null);
        });
    })->create();
