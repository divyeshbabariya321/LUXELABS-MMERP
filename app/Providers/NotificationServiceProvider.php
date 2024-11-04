<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Composers\NotificationComposer;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('partials.notifications', NotificationComposer::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
