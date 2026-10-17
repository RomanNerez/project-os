<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class StartTimeEntryRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'description' => ['required_without:task_id', 'nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'task_id' => ['required_without:description', 'nullable', 'integer', 'exists:tasks,id'],
        ];
    }
}
