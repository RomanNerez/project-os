<?php

namespace App\Containers\AppSection\Authorization\UI\WEB\Controllers;

use App\Containers\AppSection\Authorization\Actions\ListWorkspaceRolesAction;
use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\UI\API\Transformers\WorkspaceRoleTransformer;
use App\Containers\AppSection\Authorization\UI\WEB\Requests\ListWorkspaceRolesRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListWorkspaceRolesController extends WebController
{
    public function __construct(
        private readonly ListWorkspaceRolesAction $action,
    ) {
    }

    public function __invoke(ListWorkspaceRolesRequest $request): Response
    {
        return Inertia::render('settings', [
            'tab' => $request->route('tab', 'roles'),
            'roles' => fractal($this->action->run(), new WorkspaceRoleTransformer())->toArray(),
            'permissions' => array_map(
                static fn (Permission $permission): array => [
                    'name' => $permission->value,
                    'display_name' => $permission->label(),
                ],
                Permission::cases(),
            ),
        ]);
    }
}
