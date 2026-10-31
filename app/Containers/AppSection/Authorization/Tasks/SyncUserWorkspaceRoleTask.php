<?php

namespace App\Containers\AppSection\Authorization\Tasks;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class SyncUserWorkspaceRoleTask extends ParentTask
{
    /**
     * A user holds at most one workspace role; the super admin role is left untouched.
     */
    public function run(User $user, int|null $roleId): User
    {
        $user->roles()
            ->where('guard_name', Permission::GUARD)
            ->where('name', '!=', RoleEnum::SUPER_ADMIN->value)
            ->get()
            ->each(static fn ($role) => $user->removeRole($role));

        if (null !== $roleId) {
            $user->assignRole(Role::findById($roleId, Permission::GUARD));
        }

        return $user->unsetRelation('roles');
    }
}
