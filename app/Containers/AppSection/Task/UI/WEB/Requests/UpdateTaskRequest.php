<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
