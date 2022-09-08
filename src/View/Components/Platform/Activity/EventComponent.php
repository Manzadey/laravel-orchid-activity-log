<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\View\Components\Platform\Activity;

use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventComponent extends Component
{
    public function __construct(readonly public Activity $activity)
    {
    }

    public function color() : string
    {
        return match ($this->activity->event) {
            'created' => 'success',
            'deleted' => 'danger',
            default => 'primary',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() : View
    {
        return view('orchid-activity-log::components.platform.activity.event-component');
    }
}
