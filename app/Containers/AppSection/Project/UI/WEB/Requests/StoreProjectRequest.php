<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

final class StoreProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'budget' => ['required']
        ];
    }
}
