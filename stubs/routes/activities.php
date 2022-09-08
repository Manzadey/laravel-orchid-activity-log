<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Orchid\Screens\Activity\ActivityListScreen;
use App\Orchid\Screens\Activity\ActivityShowScreen;
use Illuminate\Support\Facades\Route;
use Manzadey\OrchidActivityLog\Services\ActivityService;
use Tabuna\Breadcrumbs\Trail;

Route::prefix('activities')
    ->name('activities.')
    ->group(static function() {
        Route::screen('', ActivityListScreen::class)
            ->name('list')
            ->breadcrumbs(static fn(Trail $trail) : Trail => $trail
                ->parent('platform.index')
                ->push(ActivityService::NAME, route(ActivityService::ROUTE_LIST))
            );

        Route::screen('{activity}', ActivityShowScreen::class)
            ->name('show')
            ->breadcrumbs(static fn(Trail $trail, Activity $activity) : Trail => $trail
                ->parent(ActivityService::ROUTE_LIST)
                ->push($activity->name_for_human, route(ActivityService::ROUTE_SHOW, $activity))
            );
    });
