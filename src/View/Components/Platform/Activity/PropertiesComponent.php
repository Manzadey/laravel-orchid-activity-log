<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\View\Components\Platform\Activity;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class PropertiesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        readonly public Repository $repository,
        readonly private string $field
    )
    {
        //
    }

    public function value() : ?string
    {
        $value = $this->repository->get($this->field);

        if($value === null) {
            return null;
        }

        if(is_bool($value)) {
            return $value ? '1' : '0';
        }

        return str(print_r($value, true))->prepend('<pre class="py-2 px-3">')->append('</pre>')->toString();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() : View
    {
        return view('orchid-activity-log::components.platform.activity.properties-component');
    }
}
