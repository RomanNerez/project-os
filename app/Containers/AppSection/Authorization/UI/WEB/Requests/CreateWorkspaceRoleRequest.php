<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Requests;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CreateWorkspaceRoleRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255', $this->uniqueDisplayName()],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', Rule::in(Permission::values())],
        ];
    }

    public function attributes(): array
    {
        return [
            'display_name' => 'назва',
            'description' => 'опис',
            'permissions' => 'дозволи',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class);
    }

    protected function uniqueDisplayName(): Unique
    {
        return Rule::unique('roles', 'display_name')->where('guard_name', Permission::GUARD);
    }
}
