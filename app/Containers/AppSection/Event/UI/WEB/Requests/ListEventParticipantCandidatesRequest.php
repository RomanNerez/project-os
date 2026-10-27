<?php

namespace App\Containers\AppSection\Event\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListEventParticipantCandidatesRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return !$this->filled('project_id')
            || $this->user()->can('view', Project::query()->findOrFail($this->integer('project_id')));
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer'],
        ];
    }
}
