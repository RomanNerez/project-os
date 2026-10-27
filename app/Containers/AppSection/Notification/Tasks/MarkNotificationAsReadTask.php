<?php

namespace App\Containers\AppSection\Notification\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class MarkNotificationAsReadTask extends ParentTask
{
    /**
     * Looks the notification up among the user's own ones, so a foreign id is a 404.
     */
    public function run(User $user, string $id): void
    {
        $user->notifications()->findOrFail($id)->markAsRead();
    }
}
