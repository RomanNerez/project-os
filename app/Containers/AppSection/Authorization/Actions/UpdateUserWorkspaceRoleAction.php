<?php

namespace App\Containers\AppSection\Authorization\Actions;

use App\Containers\AppSection\Authorization\Tasks\SyncUserWorkspaceRoleTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class UpdateUserWorkspaceRoleAction extends ParentAction
{
    public function __construct(
        private readonly SyncUserWorkspaceRoleTask $syncUserWorkspaceRoleTask,
    ) {
    }

    public function run(User $user, int|null $roleId): User
    {
        return DB::transaction(fn (): User => $this->syncUserWorkspaceRoleTask->run($user, $roleId));
    }
}
