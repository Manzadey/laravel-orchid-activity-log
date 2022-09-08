<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Orchid\Actions\Menu;

use App\Models\Activity;
use Manzadey\OrchidActivityLog\Services\ActivityService;
use Orchid\Screen\Actions\Menu;

class ActivityMenu
{
    public static function make() : Menu
    {
        return Menu::make(ActivityService::NAME)
            ->route(ActivityService::ROUTE_LIST)
            ->icon(ActivityService::ICON)
            ->can('list', Activity::class);
    }
}
