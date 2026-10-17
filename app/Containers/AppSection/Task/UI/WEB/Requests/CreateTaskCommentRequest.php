<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Requests\Request as ParentRequest;

class CreateTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('comment', Task::query()->findOrFail((int) $this->route('taskId')));
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
