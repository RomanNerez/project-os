<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListTimeEntriesRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('view', Project::query()->findOrFail((int) $this->route('id')));
    }

    public function rules(): array
    {
        return [];
    }
}
