<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Tasks\StopRunningTimeEntriesTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class StopTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly StopRunningTimeEntriesTask $stopRunningTimeEntriesTask,
    ) {
    }

    public function run(int $userId): int
    {
        return $this->stopRunningTimeEntriesTask->run($userId, now()->startOfSecond());
    }
}
