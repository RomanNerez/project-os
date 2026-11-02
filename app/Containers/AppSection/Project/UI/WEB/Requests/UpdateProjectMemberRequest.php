<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\ProjectUser;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProjectMemberRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('updateMember', [
            ProjectUser::class,
            $this->route('project'),
            $this->route('member'),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'role' => ['required', new Enum(ProjectRole::class)],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}
