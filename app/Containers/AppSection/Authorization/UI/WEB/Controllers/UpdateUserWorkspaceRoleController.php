<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\UpdateUserWorkspaceRoleAction;
use App\Containers\AppSection\Authorization\UI\WEB\Requests\UpdateUserWorkspaceRoleRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateUserWorkspaceRoleController extends WebController
{
    public function __construct(
        private readonly UpdateUserWorkspaceRoleAction $action,
    ) {
    }

    public function __invoke(UpdateUserWorkspaceRoleRequest $request, User $user): RedirectResponse
    {
        $this->action->run($user, $request->filled('role_id') ? $request->integer('role_id') : null);

        return back();
    }
}
