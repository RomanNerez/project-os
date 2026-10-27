<?php

namespace App\Containers\AppSection\Notification\Actions;

use App\Containers\AppSection\Notification\Tasks\ListUserNotificationsTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

final class ListNotificationsAction extends ParentAction
{
    public function __construct(
        private readonly ListUserNotificationsTask $listUserNotificationsTask,
    ) {
    }

    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function run(User $user): LengthAwarePaginator
    {
        return $this->listUserNotificationsTask->run($user);
    }
}
