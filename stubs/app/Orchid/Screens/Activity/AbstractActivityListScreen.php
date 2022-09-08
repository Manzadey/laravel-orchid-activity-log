<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Activity;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Manzadey\LaravelOrchidHelpers\Orchid\Filters\CreatedTimestampFilter;
use Manzadey\LaravelOrchidHelpers\Orchid\Filters\IdFilter;
use Manzadey\LaravelOrchidHelpers\Orchid\Filters\UserFilter;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Layouts\ModelsTableLayout;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Links\DropdownOptions;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Links\ShowLink;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Screens\AbstractScreen;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\ActionsTD;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\CreatedAtTD;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\EntityRelationTD;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\IdTD;
use Manzadey\OrchidActivityLog\View\Components\Platform\Activity\EventComponent;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Layouts\Selection;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

abstract class AbstractActivityListScreen extends AbstractScreen
{
    protected array $hiddenColumns = [];

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function layout() : iterable
    {
        $this->authorizeList(Activity::class);

        return [
            $this->selection(),

            ModelsTableLayout::make([
                IdTD::make()
                    ->filter(TD::FILTER_NUMERIC),
                TD::make('log_name', __('Название лога'))
                    ->defaultHidden()
                    ->filter(TD::FILTER_TEXT),
                TD::make('description', __('Описание'))
                    ->filter(TD::FILTER_TEXT)
                    ->defaultHidden(),
                TD::make('event', __('Событие'))
                    ->filter(TD::FILTER_SELECT)
                    ->filterOptions(Activity::EVENTS)
                    ->component(EventComponent::class),
                EntityRelationTD::make('causer', __('Пользователь')),
                TD::make('subject_type', __('Тип объекта'))
                    ->render(static fn(Activity $activity) : string => __(class_basename($activity->subject_type)))
                    ->sort()
                    ->canSee($this->isHidden('subject_type')),
                EntityRelationTD::make('subject', __('Сущность'))
                    ->canSee($this->isHidden('subject')),
                CreatedAtTD::make(),
                ActionsTD::make(static fn(Activity $activity) : DropDown => DropdownOptions::make()->list([
                    ShowLink::route('platform.activities.show', $activity),
                ])),
            ]),
        ];
    }

    public function getBuilder(Activity|Builder $builder = null) : Builder
    {
        return ($builder ?? Activity::query())
            ->filters()
            ->filtersApplySelection($this->selection())
            ->with(['causer', 'subject'])
            ->latest('id');
    }

    private function isHidden(string $key) : bool
    {
        return !in_array($key, $this->hiddenColumns, true);
    }

    private function selection() : Selection
    {
        return Layout::selection([
            IdFilter::class,
            new UserFilter('causer_id'),
            CreatedTimestampFilter::class,
        ]);
    }
}
