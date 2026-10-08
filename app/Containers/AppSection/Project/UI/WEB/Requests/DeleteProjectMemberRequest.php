<?php

namespace App\Containers\AppSection\Project\UI\WEB\Requests;

use App\Containers\AppSection\Project\Models\ProjectUser;
use App\Ship\Parents\Requests\Request as ParentRequest;

class DeleteProjectMemberRequest extends ParentRequest
{
    protected array $decode = [];

    public function authorize(): bool
    {
        return $this->user()->can('deleteMember', [
            ProjectUser::class,
            $this->route('project'),
            $this->route('member'),
        ]);
    }
}
