<?php

namespace App\Containers\AppSection\Authorization\UI\API\Transformers;

use App\Containers\AppSection\Authorization\Models\Role;

final class WorkspaceRoleTransformer extends RoleTransformer
{
    protected array $availableIncludes = [];

    public function transform(Role $role): array
    {
        return parent::transform($role) + [
            'permissions' => $role->permissions->pluck('name')->values()->all(),
            'users_count' => $role->users_count ?? 0,
        ];
    }
}
