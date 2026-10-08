<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

final class UpdateProjectRequest extends CreateProjectRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }
}
