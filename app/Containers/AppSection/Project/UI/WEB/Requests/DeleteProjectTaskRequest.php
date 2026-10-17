<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteProjectTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('deleteTask', $this->route('project'));
    }

    public function rules(): array
    {
        return [];
    }
}
