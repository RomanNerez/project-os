<?php

namespace App\Containers\AppSection\User\UI\WEB\Requests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Password;

final class UpdateProfilePasswordRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:web'],
            'new_password' => ['required', 'different:current_password', Password::default()],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'поточний пароль',
            'new_password' => 'новий пароль',
            'new_password_confirmation' => 'підтвердження пароля',
        ];
    }

    public function authorize(): bool
    {
        return $this->user('web')->can('update', [User::class, $this->user('web')->id]);
    }
}
