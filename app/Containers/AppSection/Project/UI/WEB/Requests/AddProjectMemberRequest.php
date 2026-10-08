<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\ProjectUser;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

class AddProjectMemberRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('addMember', [ProjectUser::class, $this->route('project')]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', new Enum(ProjectRole::class)],
        ];
    }
}
