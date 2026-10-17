<?php

namespace App\Containers\AppSection\User\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ShowProfileRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }
}
