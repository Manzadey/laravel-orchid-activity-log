<?php

declare(strict_types=1);

namespace Manzadey\OrchidActivityLog\Services;

class ActivityService
{
    public const NAME = 'Лог активности';

    public const PLURAL = 'activities';

    public const ROUTE = 'platform.' . self::PLURAL . '.';

    public const ROUTE_LIST = self::ROUTE . 'list';

    public const ROUTE_SHOW = self::ROUTE . 'show';

    public const ICON = 'database';
}
