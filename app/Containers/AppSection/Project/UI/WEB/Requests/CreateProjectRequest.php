<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

class CreateProjectRequest extends ParentRequest
{
    protected array $decode = [];

    /**
     * @return array
     */
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
