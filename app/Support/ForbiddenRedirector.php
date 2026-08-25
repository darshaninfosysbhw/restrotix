<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenRedirector
{
    public function handle(Request $request, ?string $message = null): Response
    {
        $user = Auth::user();
        $message = $message ?: $this->messageFor($request, $user);
        $currentRouteName = (string) optional($request->route())->getName();

        // 403 पर intended URL को reset कर दो, वरना login के बाद user फिर उसी blocked page पर लौट सकता है।
        $request->session()->forget('url.intended');

        $targetUrl = $this->resolveRedirectUrl($request, $user);
        if ($this->normalizeUrl($targetUrl) === $this->normalizeUrl($request->fullUrl())) {
            $landingUrl = $this->landingUrlForUser($user);
            $targetUrl = $this->normalizeUrl($landingUrl) !== $this->normalizeUrl($request->fullUrl())
                ? $landingUrl
                : ($currentRouteName === 'home'
                    ? $this->routeUrl('login', 'home')
                    : $this->routeUrl('home', 'login'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_to' => $targetUrl,
            ], 403);
        }

        return redirect()->to($targetUrl)
            ->with('toast', [
                [
                    'type' => $user ? 'error' : 'warning',
                    'message' => $message,
                    'duration' => 5000,
                ],
            ]);
    }

    private function resolveRedirectUrl(Request $request, ?User $user): string
    {
        $routeName = (string) optional($request->route())->getName();
        $path = trim($request->path(), '/');
        $role = strtolower(trim((string) ($user?->role ?? '')));
        $isImpersonating = session()->has('impersonated_by');

        if ($this->isSuperAdminArea($routeName, $path)) {
            if ($role === 'superadmin' && !$isImpersonating) {
                return $this->routeUrl('superadmin.dashboard', 'home');
            }

            return $this->landingUrlForUser($user);
        }

        if ($this->isAdminArea($routeName, $path)) {
            if ($role === 'superadmin' && !$isImpersonating) {
                return $this->routeUrl('superadmin.dashboard', 'home');
            }

            if ($role === 'chef') {
                return $this->routeUrl('admin.kds.index', 'admin.dashboard');
            }

            if ($role === 'waiter') {
                return $this->routeUrl('admin.waiter.index', 'admin.dashboard');
            }

            return $this->resolveAdminAreaTarget($routeName, $path);
        }

        if ($this->isWaiterArea($routeName, $path)) {
            if ($role === 'waiter') {
                return $this->routeUrl('admin.waiter.index', 'admin.dashboard');
            }

            return $this->landingUrlForUser($user);
        }

        if ($this->isPublicArea($routeName, $path)) {
            return $this->routeUrl('home', 'home');
        }

        return $this->landingUrlForUser($user);
    }

    private function resolveAdminAreaTarget(string $routeName, string $path): string
    {
        $current = $routeName !== '' ? $routeName : $path;

        $map = [
            'admin.branches.index' => 'admin.branches.index',
            'admin.branches.payment-gateways' => 'admin.branches.payment-gateways',
            'admin.employee.index' => 'admin.employee.index',
            'admin.settings.menu.index' => 'admin.settings.menu.index',
            'admin.menu.categories.index' => 'admin.menu.categories.index',
            'admin.menu.items.index' => 'admin.menu.items.index',
            'menu.items' => 'admin.menu.items.index',
            'admin.tables.index' => 'admin.tables.index',
            'admin.order.index' => 'admin.order.index',
            'admin.orders.history' => 'admin.orders.history',
            'admin.billing' => 'admin.billing',
            'admin.kds.index' => 'admin.kds.index',
            'admin.profile' => 'admin.profile',
        ];

        foreach ($map as $needle => $targetRoute) {
            if ($routeName === $needle || Str::startsWith($routeName, $needle . '.') || Str::startsWith($path, str_replace('.', '/', $needle))) {
                return $this->routeUrl($targetRoute, 'admin.dashboard');
            }
        }

        // If we cannot map the exact admin section, send the user to the normal admin landing.
        return $this->routeUrl('admin.dashboard', 'home');
    }

    private function landingUrlForUser(?User $user): string
    {
        $role = strtolower(trim((string) ($user?->role ?? '')));

        return match ($role) {
            'superadmin' => $this->routeUrl('superadmin.dashboard', 'home'),
            'chef' => $this->routeUrl('admin.kds.index', 'home'),
            'waiter' => $this->routeUrl('admin.waiter.index', 'home'),
            default => $user ? $this->routeUrl('admin.dashboard', 'home') : $this->routeUrl('login', 'home'),
        };
    }

    private function messageFor(Request $request, ?User $user): string
    {
        $routeName = (string) optional($request->route())->getName();
        $role = strtolower(trim((string) ($user?->role ?? '')));
        $path = trim($request->path(), '/');

        if ($this->isSuperAdminArea($routeName, $path) && $role !== 'superadmin') {
            return 'Yeh Super Admin section hai. Aapko is page ka access nahi hai.';
        }

        if ($this->isAdminArea($routeName, $path) && $role === 'superadmin' && !session()->has('impersonated_by')) {
            return 'Aap Super Admin panel me ho. Admin panel ke liye impersonate use karein.';
        }

        if ($routeName === 'admin.branches.payment-gateways' || Str::startsWith($routeName, 'admin.branches.')) {
            return 'Aapko Branches section ka access nahi hai.';
        }

        if ($routeName === 'admin.employee.index' || Str::startsWith($routeName, 'admin.employee.')) {
            return 'Aapko Employees section ka access nahi hai.';
        }

        if ($routeName === 'admin.settings.menu.index' || Str::startsWith($routeName, 'admin.settings.menu.')) {
            return 'Aapko Menu Settings ka access nahi hai.';
        }

        if ($routeName === 'admin.menu.categories.index' || Str::startsWith($routeName, 'admin.menu.categories.')) {
            return 'Aapko Categories section ka access nahi hai.';
        }

        if ($routeName === 'admin.menu.items.index' || $routeName === 'menu.items' || Str::startsWith($routeName, 'admin.menu.items.')) {
            return 'Aapko Menu Items section ka access nahi hai.';
        }

        if ($routeName === 'admin.tables.index' || Str::startsWith($routeName, 'admin.tables.')) {
            return 'Aapko Tables section ka access nahi hai.';
        }

        if ($routeName === 'admin.order.index') {
            return 'Aapko Manual Orders section ka access nahi hai.';
        }

        if ($routeName === 'admin.orders.history' || Str::startsWith($routeName, 'admin.orders.history')) {
            return 'Aapko Order History ka access nahi hai.';
        }

        if ($routeName === 'admin.billing' || Str::startsWith($routeName, 'admin.billing')) {
            return 'Aapko Billing page ka access nahi hai.';
        }

        if ($routeName === 'admin.kds.index' || Str::startsWith($routeName, 'admin.kds.')) {
            return 'Aapko KDS section ka access nahi hai.';
        }

        if ($this->isWaiterArea($routeName, $path)) {
            return 'Yeh Waiter section hai. Aapko is page ka access nahi hai.';
        }

        if ($this->isPublicArea($routeName, $path)) {
            return 'Aapko is public page ka access nahi hai.';
        }

        return 'Aapko is page ka access nahi hai.';
    }

    private function isSuperAdminArea(string $routeName, string $path): bool
    {
        return Str::startsWith($routeName, 'superadmin.') || Str::startsWith($path, 'superadmin');
    }

    private function isAdminArea(string $routeName, string $path): bool
    {
        return Str::startsWith($routeName, 'admin.') || Str::startsWith($path, 'admin');
    }

    private function isWaiterArea(string $routeName, string $path): bool
    {
        return Str::startsWith($routeName, 'waiter.') || Str::startsWith($path, 'waiter');
    }

    private function isPublicArea(string $routeName, string $path): bool
    {
        return Str::startsWith($routeName, 'public.') || Str::startsWith($path, 'menu') || in_array($routeName, ['home', 'checkout', 'checkout.store'], true);
    }

    private function routeUrl(string $routeName, string $fallbackRouteName): string
    {
        if (Route::has($routeName)) {
            return route($routeName);
        }

        return Route::has($fallbackRouteName) ? route($fallbackRouteName) : url('/');
    }

    private function normalizeUrl(string $url): string
    {
        return rtrim($url, '/');
    }
}
