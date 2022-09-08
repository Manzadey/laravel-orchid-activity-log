<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Manzadey\LaravelOrchidHelpers\Services\PlatformPermissionService;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function list(User $user) : bool
    {
        return $user->hasAccess(PlatformPermissionService::getPermission(Activity::class, 'list'));
    }

    public function show(User $user) : bool
    {
        return $user->hasAccess(PlatformPermissionService::getPermission(Activity::class, 'show'));
    }
}
