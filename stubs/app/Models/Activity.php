<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Manzadey\OrchidActivityLog\Orchid\Presenters\ActivityLogPresenter;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Screen\Repository;
use Spatie\Activitylog\Models\Activity as BaseActivity;

class Activity extends BaseActivity
{
    use AsSource;
    use Filterable;

    protected array $allowedFilters = [
        'id',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'created_at',
    ];

    protected array $allowedSorts = [
        'id',
        'event',
        'created_at',
        'subject_type',
    ];

    public const EVENTS = [
        'created' => 'Добавлено',
        'updated' => 'Обновлено',
        'deleted' => 'Удалено',
    ];

    public function repositoryProperties() : Attribute
    {
        return Attribute::make(
            fn() : Collection => $this->properties?->map(static fn($item) => is_array($item) ? new Repository($item) : null)->filter() ?? collect()
        );
    }

    public function nameForHuman() : Attribute
    {
        return Attribute::make(
            fn() : string => "#$this->id от {$this->created_at?->isoFormat('LLLL')}"
        );
    }

    public function eventName() : Attribute
    {
        return Attribute::make(
            fn() : string => self::EVENTS[$this->getAttribute('event')] ?? ''
        );
    }

    public function presenter() : ActivityLogPresenter
    {
        return new ActivityLogPresenter($this);
    }
}
