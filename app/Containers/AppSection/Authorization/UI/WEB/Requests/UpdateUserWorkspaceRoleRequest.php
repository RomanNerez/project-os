<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateUserWorkspaceRoleRequest extends ParentRequest
{
    protected array $decode = ['role_id'];

    public function rules(): array
    {
        return [
            'role_id' => ['present', 'nullable', 'integer', Role::workspaceRoleExistsRule()],
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => 'роль',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('assign', Role::class);
    }
}
