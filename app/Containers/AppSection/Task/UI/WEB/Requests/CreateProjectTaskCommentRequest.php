<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class CreateProjectTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('comment', $this->route('task'));
    }

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
