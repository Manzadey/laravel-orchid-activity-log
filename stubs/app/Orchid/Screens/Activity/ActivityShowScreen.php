<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Activity;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use JetBrains\PhpStorm\ArrayShape;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Layouts\ModelLegendLayout;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Screens\ModelScreen;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Sights\CreatedAtSight;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Sights\EntitySight;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\Sights\IdSight;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\CreatedAtTD;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\EntityRelationTD;
use Manzadey\LaravelOrchidHelpers\Orchid\Helpers\TD\IdTD;
use Manzadey\OrchidActivityLog\View\Components\Platform\Activity\PropertiesComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

/**
 * @property Activity $model
 */
class ActivityShowScreen extends ModelScreen
{
    private bool $isSeeRelatedLayout = false;

    /**
     * Query data.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return array
     */
    #[ArrayShape([0 => 'iterable', 'related' => 'mixed'])]
    public function query(Activity $activity) : iterable
    {
        $this->authorizeShow($activity);

        $related = $activity->newQuery()
            ->with('causer')
            ->where(static fn(Builder $builder) : Builder => $builder
                ->where('subject_type', $activity->subject_type)
                ->where('subject_id', $activity->subject_id)
            )
            ->when($activity->batch_uuid !== null, static fn(Builder $builder) : Builder => $builder
                ->orWhere('batch_uuid', $activity->batch_uuid)
            )
            ->latest('id')
            ->get([
                'id', 'causer_id', 'causer_type', 'created_at', 'event',
            ]);

        $this->isSeeRelatedLayout = $related->count() > 1;

        return [
            ...$this->model($activity),
            'related' => $related,
        ];
    }

    public function commandBar() : iterable
    {
        return [
            Button::make('Revert')
                ->icon('action-undo')
                ->confirm(__('activities.confirm_revert'))
                ->method('revert', [
                    'id' => $this->model->id,
                ])
                ->can('edit', $this->model->subject),
        ];
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function revert(Activity $activity) : RedirectResponse
    {
        $this->authorize('edit', $activity);

        $activity
            ->subject
            ->fill($activity->properties->get('old'))
            ->save();

        if($activity->subject->wasChanged()) {
            $activity = $activity
                ->subject
                ->activities()
                ->where('subject_type', $activity->subject_type)
                ->where('subject_id', $activity->subject_id)
                ->latest('id')
                ->first();
        }

        return to_route('platform.activities.show', $activity);
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout() : iterable
    {
        $id = $this->model->id;

        $layouts = [
            Layout::columns([
                ModelLegendLayout::make([
                    IdSight::make(),
                    Sight::make('log_name', __('???????????????? ????????')),
                    Sight::make('description', __('????????????????')),
                    Sight::make('event', __('??????????????')),
                    Sight::make('batch_uuid', __('UUID')),
                    EntitySight::make('causer', __('????????????????????????')),
                    CreatedAtSight::make(),
                ])->title(__('??????????????????')),
            ])->canSee($this->isSeeRelatedLayout),

            ModelLegendLayout::make([
                EntitySight::make('subject', __('????????????')),
                Sight::make('subject_type', __('??????')),
                Sight::make('subject_id', __('ID')),
            ])->title(__('????????????')),

            ModelLegendLayout::make([
                EntitySight::make('causer', __('????????????????????????')),
                Sight::make('causer_type', __('??????')),
                Sight::make('causer_id', __('ID')),
            ])->title(__('????????????????????????')),
        ];

        if($this->model->repository_properties->isNotEmpty()) {
            $columns = [];

            foreach (['attributes', 'old'] as $status) {
                if($this->model->repository_properties->get($status)) {
                    $sights = collect($this->model->properties->get($status))
                        ->keys()
                        ->map(static fn(string $field) : Sight => Sight::make($field, attrName($field))
                            ->component(PropertiesComponent::class, compact('field'))
                        )
                        ->toArray();

                    $columns[] = Layout::legend("model.repository_properties.$status", $sights)
                        ->title(__("activities.$status"));
                }
            }

            $layouts[] = Layout::columns($columns);
        }

        return [
            Layout::tabs([
                __('????????????????') => [
                    $layouts,
                ],

                __('?????????????????? ??????????????????') => [
                    Layout::table('related', [
                        IdTD::make()
                            ->defaultHidden(false)
                            ->render(static fn(Activity $activity) : Link => Link::make((string) $activity->id)
                                ->route('platform.activities.show', $activity)
                                ->when($id === $activity->id, static fn(Link $link) : Link => $link->type(Color::SUCCESS()))
                            ),
                        TD::make('event', __('??????????????')),
                        EntityRelationTD::make('causer', __('????????????????????????')),
                        CreatedAtTD::make()->alignRight(),
                    ])->title(__('?????????????????? ??????????????????')),
                ],
            ]),
        ];
    }
}
