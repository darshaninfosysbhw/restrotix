<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Toast Manager component
        Blade::component('core.components.toast-manager', 'toast-manager');

        // Register core components folder with namespace
        // This allows: <x-core.landing.hero /> and <x-core.ui.button />
        Blade::anonymousComponentPath(resource_path('views/core/components'), 'core');

        // Register modules folder
        Blade::anonymousComponentPath(resource_path('views/modules'), 'modules');
    }
}
