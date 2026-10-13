<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

class CreateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        $projectId = $this->input('project_id');

        if (null === $projectId) {
            return true;
        }

        $project = Project::find($projectId);

        return null === $project || $this->user()->can('createTask', $project);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }
}
