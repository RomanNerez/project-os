<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\ListProjectsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListProjectsAction extends ParentAction
{
    public function __construct(
        private readonly ListProjectsTask $listProjectsTask
    ) {}

    /**
     * @return LengthAwarePaginator<int, Project>
     */
    public function run(): LengthAwarePaginator
    {
        return $this->listProjectsTask->run();
    }
}