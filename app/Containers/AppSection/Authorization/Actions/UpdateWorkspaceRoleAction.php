<?php

namespace App\Containers\AppSection\Authorization\Actions;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\Tasks\SaveWorkspaceRoleTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class UpdateWorkspaceRoleAction extends ParentAction
{
    public function __construct(
        private readonly SaveWorkspaceRoleTask $saveWorkspaceRoleTask,
    ) {
    }

    /**
     * @param array{display_name: string, description?: string|null, permissions?: list<string>} $data
     */
    public function run(Role $role, array $data): Role
    {
        return DB::transaction(fn (): Role => $this->saveWorkspaceRoleTask->run($data, $role));
    }
}
