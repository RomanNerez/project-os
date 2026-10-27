<?php

namespace App\Containers\AppSection\Notification\Actions;

use App\Containers\AppSection\Notification\Tasks\MarkNotificationAsReadTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;

final class MarkNotificationAsReadAction extends ParentAction
{
    public function __construct(
        private readonly MarkNotificationAsReadTask $markNotificationAsReadTask,
    ) {
    }

    public function run(User $user, string $id): void
    {
        $this->markNotificationAsReadTask->run($user, $id);
    }
}
