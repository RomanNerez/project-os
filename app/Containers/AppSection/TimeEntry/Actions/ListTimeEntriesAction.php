<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tasks\ListTimeEntriesTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListTimeEntriesAction extends ParentAction
{
    public function __construct(
        private readonly ListTimeEntriesTask $listTimeEntriesTask,
    ) {
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function run(int $userId): Collection
    {
        return $this->listTimeEntriesTask->run($userId);
    }
}
