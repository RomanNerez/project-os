<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class CreateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255']
        ];
    }
}
