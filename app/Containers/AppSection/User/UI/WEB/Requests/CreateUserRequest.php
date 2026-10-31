<?php

namespace App\Containers\AppSection\User\UI\WEB\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Password;

final class CreateUserRequest extends ParentRequest
{
    protected array $decode = ['role_id'];

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::default(),
            ],
            'role_id' => ['nullable', 'integer', Role::workspaceRoleExistsRule()],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'імʼя',
            'last_name' => 'прізвище',
            'password' => 'пароль',
            'role_id' => 'роль',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', [User::class]);
    }
}
