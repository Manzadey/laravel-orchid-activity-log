<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Console\Commands;

use Illuminate\Console\Command;
use Manzadey\OrchidActivityLog\Providers\FoundationServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'orchid-activity-log:install';

    protected $description = 'Install Orchid Activity Log';

    public function handle() : int
    {
        if($this->components->confirm('Install Activity Log migrations?', true)) {
            $this->callActivityLogVendors([
                'activitylog-migrations',
            ]);
        }

        if($this->components->confirm('Install Activity Log config?', true)) {
            $this->callActivityLogVendors([
                'activitylog-config',
            ]);
        }

        $this->call('vendor:publish', [
            '--provider' => FoundationServiceProvider::class,
            '--tag'      => [
                'orchid-activity-log',
            ],
        ]);

        $this->components->info('Package installed!');

        return 1;
    }

    private function callActivityLogVendors(array $tags) : void
    {
        $this->call('vendor:publish', [
            '--provider' => ActivitylogServiceProvider::class,
            '--tag'      => $tags,
        ]);
    }
}
