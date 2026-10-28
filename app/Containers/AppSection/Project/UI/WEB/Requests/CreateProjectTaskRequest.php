<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Task\Enums\TaskPriority;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

class CreateProjectTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('createTask', $this->route('project'));
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'priority' => ['nullable', new Enum(TaskPriority::class)],
            'estimate' => ['nullable', 'integer', 'min:1', 'max:60000'],
        ];
    }
}
