<?php

namespace App\Containers\AppSection\Invitation\Tasks;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\Date;

final class AcceptInvitationTask extends ParentTask
{
    public function run(Invitation $invitation): Invitation
    {
        $invitation->update(['accepted_at' => Date::now()]);

        return $invitation;
    }
}
