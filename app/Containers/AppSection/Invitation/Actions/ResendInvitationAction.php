<?php

namespace App\Containers\AppSection\Invitation\Actions;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Validation\ValidationException;

final class ResendInvitationAction extends ParentAction
{
    public function __construct(
        private readonly InviteUserAction $inviteUserAction,
    ) {
    }

    /**
     * Only the token hash is stored, so the old link can't be sent again:
     * a fresh invitation replaces it with a new link and a new expiry date.
     */
    public function run(int $id, User $inviter): Invitation
    {
        $invitation = Invitation::query()->findOrFail($id);

        abort_unless($invitation->isUsable(), 404);

        if (User::query()->where('email', $invitation->email)->exists()) {
            throw ValidationException::withMessages(['email' => 'Користувач з таким email уже зареєстрований.']);
        }

        return $this->inviteUserAction->run($invitation->email, $inviter);
    }
}
