<?php

namespace App\Containers\AppSection\Notification\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

final class ListUserNotificationsTask extends ParentTask
{
    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function run(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }
}
