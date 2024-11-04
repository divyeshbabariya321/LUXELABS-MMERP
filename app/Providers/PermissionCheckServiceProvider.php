<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as ViewFactory;

class PermissionCheckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(ViewFactory $view): void
    {
        // $view->composer('*', \App\Http\Composers\GlobalComposer::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        
    }
}
