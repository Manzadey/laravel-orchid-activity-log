<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Orchid\Presenters;

use Illuminate\Support\Facades\Route;
use Orchid\Screen\Contracts\Personable;
use Orchid\Support\Presenter;

/**
 * @property \App\Models\ActivityLog $entity
 */
class ActivityLogPresenter extends Presenter implements Personable
{
    public function title() : string
    {
        $title = $this->entity->event_name;

        if($this->entity->causer) {
            $title .= ". {$this->entity->causer->email}";
        }

        return $title;
    }

    public function subTitle() : string
    {
        return $this->entity->created_at_human;
    }

    public function url() : string
    {
        $hasRoute = Route::has('platform.activities.show');

        if($hasRoute) {
            return route('platform.activities.show', $this->entity);
        }

        return '#';
    }

    public function image() : ?string
    {
        return null;
    }
}
