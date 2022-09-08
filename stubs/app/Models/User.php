<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends \Orchid\Platform\Models\User
{
    use CausesActivity;
    use LogsActivity;

    public function getActivityLogOptions() : LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
