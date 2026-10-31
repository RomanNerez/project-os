<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\ListWorkspaceRolesAction;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\UI\API\Transformers\WorkspaceRoleTransformer;
use App\Containers\AppSection\Invitation\Actions\ListPendingInvitationsAction;
use App\Containers\AppSection\Invitation\UI\API\Transformers\InvitationTransformer;
use App\Containers\AppSection\User\Actions\ListUsersAction;
use App\Containers\AppSection\User\UI\API\Transformers\UserAdminTransformer;
use App\Containers\AppSection\User\UI\WEB\Requests\ListUsersRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListUsersController extends WebController
{
    public function __construct(
        private readonly ListUsersAction $action,
        private readonly ListPendingInvitationsAction $listPendingInvitationsAction,
        private readonly ListWorkspaceRolesAction $listWorkspaceRolesAction,
    ) {
    }

    public function __invoke(ListUsersRequest $request): Response
    {
        $users = fractal($this->action->run(), new UserAdminTransformer())
            ->parseIncludes(['roles'])
            ->toArray();

        return Inertia::render('users', [
            'users' => $users,
            'invitations' => fn (): array => fractal($this->listPendingInvitationsAction->run(), new InvitationTransformer())->toArray(),
            // Only role managers can assign roles, so only they need the options.
            'roles' => fn (): array|null => $request->user()->can('assign', Role::class)
                ? fractal($this->listWorkspaceRolesAction->run(), new WorkspaceRoleTransformer())->toArray()
                : null,
        ]);
    }
}
