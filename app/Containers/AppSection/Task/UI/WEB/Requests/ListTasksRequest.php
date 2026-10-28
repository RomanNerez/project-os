<?php

namespace App\Containers\AppSection\Task\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListTasksRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * Project tasks (`projects/{id}/tasks`, `projects/{id}/tasks/options`) are open only to those who can view the project.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', Project::query()->findOrFail((int) $this->route('id')));
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:512'],
            'searchJoin' => ['nullable', 'string', 'in:and,or'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'in:10,20,50'],
        ];
    }
}
