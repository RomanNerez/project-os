<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }
}
