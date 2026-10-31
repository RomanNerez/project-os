<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteWorkspaceRoleRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        $role = $this->route('role');

        return $this->user()->can('delete', Role::class)
            && $role instanceof Role
            && $role->isWorkspaceRole();
    }
}
