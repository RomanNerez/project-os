<?php

namespace App\Containers\AppSection\User\UI\WEB\Requests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateProfileRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'імʼя',
            'last_name' => 'прізвище',
            'avatar' => 'аватар',
        ];
    }

    public function authorize(): bool
    {
        return $this->user('web')->can('update', [User::class, $this->user('web')->id]);
    }
}
