<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\UpdateWorkspaceRoleAction;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\UI\WEB\Requests\UpdateWorkspaceRoleRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateWorkspaceRoleController extends WebController
{
    public function __construct(
        private readonly UpdateWorkspaceRoleAction $action,
    ) {
    }

    public function __invoke(UpdateWorkspaceRoleRequest $request, Role $role): RedirectResponse
    {
        $this->action->run($role, $request->validated());

        return back();
    }
}
