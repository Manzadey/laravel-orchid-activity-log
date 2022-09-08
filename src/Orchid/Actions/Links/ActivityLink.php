<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Orchid\Actions\Links;

use App\Models\Activity;
use Manzadey\OrchidActivityLog\Services\ActivityService;
use Orchid\Screen\Actions\Link;

class ActivityLink
{
    public static function make() : Link
    {
        return Link::make(ActivityService::NAME)
            ->route(ActivityService::ROUTE_LIST)
            ->icon(ActivityService::ICON)
            ->can('list', Activity::class);
    }
}
