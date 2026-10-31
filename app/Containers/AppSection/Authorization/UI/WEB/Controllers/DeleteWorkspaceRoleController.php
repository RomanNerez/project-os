<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\DeleteRoleAction;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\UI\WEB\Requests\DeleteWorkspaceRoleRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteWorkspaceRoleController extends WebController
{
    public function __construct(
        private readonly DeleteRoleAction $action,
    ) {
    }

    public function __invoke(DeleteWorkspaceRoleRequest $request, Role $role): RedirectResponse
    {
        $this->action->run((int) $role->getKey());

        return back();
    }
}
