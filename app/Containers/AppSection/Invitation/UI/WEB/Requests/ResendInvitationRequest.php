<?php

namespace App\Containers\AppSection\Invitation\UI\WEB\Requests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class ResendInvitationRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', [User::class]);
    }
}
