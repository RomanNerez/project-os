<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskPriority;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class UpdateProjectTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('updateTask', $this->route('project'));
    }

    /**
     * An emptied rich-text editor still sends markup like `<p></p>`; store it as no description.
     */
    protected function prepareForValidation(): void
    {
        $description = $this->input('description');

        if (\is_string($description) && '' === trim(strip_tags(str_replace('&nbsp;', ' ', $description)))) {
            $this->merge(['description' => null]);
        }
    }

    /**
     * Every field is optional so the UI can save one field at a time.
     *
     * @return array
     */
    public function rules(): array
    {
        /** @var Project $project */
        $project = $this->route('project');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', 'required', new Enum(TaskPriority::class)],
            'estimate' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:60000'],
            'assignee_id' => ['sometimes', 'nullable', 'integer', Rule::in($project->participantIds()->all())],
        ];
    }
}
