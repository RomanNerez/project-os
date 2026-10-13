<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        $task = Task::find($this->route('id'));

        if (null === $task) {
            return true;
        }

        if (!$this->user()->can('update', $task)) {
            return false;
        }

        $projectId = $this->input('project_id');

        if (null === $projectId || (int) $projectId === $task->project_id) {
            return true;
        }

        // Moving the task: the user must be able to add tasks to the target project.
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
            'description' => ['nullable', 'string'],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
