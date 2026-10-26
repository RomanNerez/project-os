<?php

namespace App\Containers\AppSection\User\UI\WEB\Requests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListUsersRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'in:10,20,50'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('index', [User::class]);
    }
}
