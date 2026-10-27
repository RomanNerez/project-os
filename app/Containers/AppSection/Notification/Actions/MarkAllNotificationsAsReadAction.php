<?php

namespace App\Containers\AppSection\Notification\Actions;

use App\Containers\AppSection\Notification\Tasks\MarkAllNotificationsAsReadTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;

final class MarkAllNotificationsAsReadAction extends ParentAction
{
    public function __construct(
        private readonly MarkAllNotificationsAsReadTask $markAllNotificationsAsReadTask,
    ) {
    }

    public function run(User $user): void
    {
        $this->markAllNotificationsAsReadTask->run($user);
    }
}
