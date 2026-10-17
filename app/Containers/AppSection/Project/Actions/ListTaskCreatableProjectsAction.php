<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\ListTaskCreatableProjectsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListTaskCreatableProjectsAction extends ParentAction
{
    public function __construct(
        private readonly ListTaskCreatableProjectsTask $listTaskCreatableProjectsTask
    ) {}

    /**
     * @param int $userId
     * @return Collection<int, Project>
     */
    public function run(int $userId): Collection
    {
        return $this->listTaskCreatableProjectsTask->run($userId);
    }
}
