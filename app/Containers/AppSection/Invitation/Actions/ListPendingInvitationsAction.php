<?php

namespace App\Containers\AppSection\Invitation\Actions;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\Invitation\Tasks\ListPendingInvitationsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Database\Eloquent\Collection;

final class ListPendingInvitationsAction extends ParentAction
{
    public function __construct(
        private readonly ListPendingInvitationsTask $listPendingInvitationsTask,
    ) {
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function run(): Collection
    {
        return $this->listPendingInvitationsTask->run();
    }
}
