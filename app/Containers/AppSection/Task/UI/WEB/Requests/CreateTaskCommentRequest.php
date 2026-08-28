<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class CreateTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
        ];
    }
}
