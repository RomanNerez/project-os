<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Password;

final class ResetPasswordRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                Password::default(),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'password' => 'пароль',
        ];
    }
}
