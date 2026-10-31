<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use Illuminate\Validation\Rules\Unique;

final class UpdateWorkspaceRoleRequest extends CreateWorkspaceRoleRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class)
            && $this->workspaceRole()->isWorkspaceRole();
    }

    protected function uniqueDisplayName(): Unique
    {
        return parent::uniqueDisplayName()->ignore($this->workspaceRole()->id);
    }

    private function workspaceRole(): Role
    {
        $role = $this->route('role');
        assert($role instanceof Role);

        return $role;
    }
}
