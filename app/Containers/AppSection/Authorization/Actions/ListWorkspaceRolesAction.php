<?php

namespace App\Containers\AppSection\Authorization\Actions;

use App\Containers\AppSection\Authorization\Data\Collections\RoleCollection;
use App\Containers\AppSection\Authorization\Tasks\ListWorkspaceRolesTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class ListWorkspaceRolesAction extends ParentAction
{
    public function __construct(
        private readonly ListWorkspaceRolesTask $listWorkspaceRolesTask,
    ) {
    }

    public function run(): RoleCollection
    {
        return $this->listWorkspaceRolesTask->run();
    }
}
