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
        Blade::anonymousComponentPath(resource_path('views/core/components'), 'core');
        // Sirf modules folder ko point karo
        Blade::anonymousComponentPath(resource_path('views/modules'), 'modules');
    }
}
