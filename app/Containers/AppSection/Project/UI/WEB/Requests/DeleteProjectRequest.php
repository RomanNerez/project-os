<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }
}
