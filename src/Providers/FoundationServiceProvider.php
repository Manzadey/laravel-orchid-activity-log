<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Manzadey\OrchidActivityLog\Console\Commands\InstallCommand;
use Manzadey\OrchidActivityLog\Console\Commands\InstallScreensCommand;

class FoundationServiceProvider extends ServiceProvider
{
    public function register() : void
    {
        $this->commands([
            InstallCommand::class,
            InstallScreensCommand::class,
        ]);

        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views', 'orchid-activity-log'
        );

        $this->publishes([
            __DIR__ . '/../../stubs/app'    => app_path(),
            __DIR__ . '/../../stubs/routes' => base_path('routes/platform'),
        ], 'orchid-activity-log');

        $this->publishes([
            __DIR__ . '/../../stubs/app/Orchid/Screens/Activity' => app_path('Orchid/Screens/Activity'),
        ], 'orchid-activity-log-screens');
    }
}
