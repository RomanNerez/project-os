<?php

namespace App\Containers\AppSection\Notification\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class MarkAllNotificationsAsReadTask extends ParentTask
{
    public function run(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }
}
