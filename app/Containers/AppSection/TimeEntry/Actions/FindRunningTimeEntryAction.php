<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tasks\FindRunningTimeEntryTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class FindRunningTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly FindRunningTimeEntryTask $findRunningTimeEntryTask,
    ) {
    }

    public function run(int $userId): ?TimeEntry
    {
        return $this->findRunningTimeEntryTask->run($userId);
    }
}
