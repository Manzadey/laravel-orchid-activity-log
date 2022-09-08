<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Activity;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\ArrayShape;

class ActivityListScreen extends AbstractActivityListScreen
{
    #[ArrayShape(['models' => LengthAwarePaginator::class])]
    public function query() : array
    {
        return [
            'models' => $this
                ->getBuilder()
                ->paginate(30),
        ];
    }
}
