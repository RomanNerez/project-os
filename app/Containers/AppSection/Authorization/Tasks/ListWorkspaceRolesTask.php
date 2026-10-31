<?php

namespace App\Containers\AppSection\Authorization\Tasks;

use App\Containers\AppSection\Authorization\Data\Collections\RoleCollection;
use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class ListWorkspaceRolesTask extends ParentTask
{
    /**
     * Workspace roles are the "web" guard roles except the system super admin role.
     */
    public function run(): RoleCollection
    {
        return Role::query()
            ->where('guard_name', Permission::GUARD)
            ->where('name', '!=', RoleEnum::SUPER_ADMIN->value)
            ->with('permissions')
            ->withCount('users')
            ->orderBy('display_name')
            ->get();
    }
}
