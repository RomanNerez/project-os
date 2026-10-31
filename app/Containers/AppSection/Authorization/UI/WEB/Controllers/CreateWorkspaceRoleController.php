<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\CreateWorkspaceRoleAction;
use App\Containers\AppSection\Authorization\UI\WEB\Requests\CreateWorkspaceRoleRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateWorkspaceRoleController extends WebController
{
    public function __construct(
        private readonly CreateWorkspaceRoleAction $action,
    ) {
    }

    public function __invoke(CreateWorkspaceRoleRequest $request): RedirectResponse
    {
        $this->action->run($request->validated());

        return back();
    }
}
