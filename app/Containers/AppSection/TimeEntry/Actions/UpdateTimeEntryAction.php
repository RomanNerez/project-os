<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tasks\UpdateTimeEntryTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly UpdateTimeEntryTask $updateTimeEntryTask,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function run(int $id, int $userId, int $projectId, array $data): TimeEntry
    {
        return $this->updateTimeEntryTask->run($id, $userId, $projectId, $data);
    }
}
