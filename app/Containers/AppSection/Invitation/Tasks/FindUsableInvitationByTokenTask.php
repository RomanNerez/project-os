<?php

namespace App\Containers\AppSection\Invitation\Tasks;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindUsableInvitationByTokenTask extends ParentTask
{
    public function run(string|null $token, bool $lock = false): Invitation|null
    {
        if (null === $token || '' === $token) {
            return null;
        }

        $invitation = Invitation::query()
            ->where('token_hash', Invitation::hashToken($token))
            ->when($lock, static fn ($query) => $query->lockForUpdate())
            ->first();

        return true === $invitation?->isUsable() ? $invitation : null;
    }
}
