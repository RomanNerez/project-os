<?php

namespace App\Containers\AppSection\Invitation\Actions;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\Invitation\Notifications\InvitationNotification;
use App\Containers\AppSection\Invitation\Tasks\CreateInvitationTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class InviteUserAction extends ParentAction
{
    public function __construct(
        private readonly CreateInvitationTask $createInvitationTask,
    ) {
    }

    public function run(string $email, User $inviter): Invitation
    {
        [$invitation, $token] = DB::transaction(fn (): array => $this->createInvitationTask->run($email, $inviter->id));

        Notification::route('mail', $invitation->email)
            ->notify(new InvitationNotification($token, $inviter->name, $invitation->expires_at));

        return $invitation;
    }
}
