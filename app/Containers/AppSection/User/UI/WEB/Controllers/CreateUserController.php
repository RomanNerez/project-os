<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\UpdateUserWorkspaceRoleAction;
use App\Containers\AppSection\User\Actions\CreateUserAction;
use App\Containers\AppSection\User\UI\WEB\Requests\CreateUserRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

final class CreateUserController extends WebController
{
    public function __construct(
        private readonly CreateUserAction $action,
        private readonly UpdateUserWorkspaceRoleAction $updateUserWorkspaceRoleAction,
    ) {
    }

    public function __invoke(CreateUserRequest $request): RedirectResponse
    {
        $user = $this->action->run(Arr::except($request->validated(), ['role_id']));

        if ($request->filled('role_id')) {
            $this->updateUserWorkspaceRoleAction->run($user, $request->integer('role_id'));
        }

        return back();
    }
}
