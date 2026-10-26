<?php

namespace App\Containers\AppSection\Invitation\UI\WEB\Requests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class InviteUserRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Користувач з таким email уже зареєстрований.',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', [User::class]);
    }
}
