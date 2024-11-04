<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\SendgridEventRepository;
use App\Repositories\SendgridEventRepositoryDisabled;
use App\Repositories\SendgridEventRepositoryInterface;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/sendgridevents.php', 'sendgridevents');

        if (config('sendgridevents.store_events_into_database') || $this->app->runningUnitTests()) {
            $this->app->bind(SendgridEventRepositoryInterface::class, SendgridEventRepository::class);
        } else {
            $this->app->bind(SendgridEventRepositoryInterface::class, SendgridEventRepositoryDisabled::class);
        }
    }

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/sendgridevents.php' => config_path('sendgridevents.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/sendgridevents.php');

        if (config('sendgridevents.store_events_into_database') || $this->app->runningUnitTests()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
