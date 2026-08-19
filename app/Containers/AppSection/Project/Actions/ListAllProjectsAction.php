<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\ListAllProjectsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListAllProjectsAction extends ParentAction
{
    public function __construct(
        private readonly ListAllProjectsTask $listAllProjectsTask
    ) {}

    /**
     * @return Collection<int, Project>
     */
    public function run(): Collection
    {
        return $this->listAllProjectsTask->run();
    }
}
