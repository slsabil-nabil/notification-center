<?php

namespace Slsabil\NotificationCenter;

use Illuminate\Support\ServiceProvider;

class NotificationCenterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/notification_center.php',
            'notification_center'
        );
    }

    public function boot(): void
    {
        // Routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notification-center');

        // Publish
        $this->publishes([
            __DIR__.'/../config/notification_center.php' => config_path('notification_center.php'),
        ], 'notification-center-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/notification-center'),
        ], 'notification-center-views');
    }
}
