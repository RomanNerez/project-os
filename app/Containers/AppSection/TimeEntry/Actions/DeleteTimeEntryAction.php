<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Tasks\DeleteTimeEntryTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly DeleteTimeEntryTask $deleteTimeEntryTask,
    ) {
    }

    public function run(int $id, int $userId): bool
    {
        return $this->deleteTimeEntryTask->run($id, $userId);
    }
}
