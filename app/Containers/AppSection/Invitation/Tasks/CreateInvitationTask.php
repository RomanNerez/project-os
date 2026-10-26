<?php

namespace App\Containers\AppSection\Invitation\Tasks;

use App\Containers\AppSection\Invitation\Data\Repositories\InvitationRepository;
use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

final class CreateInvitationTask extends ParentTask
{
    public function __construct(
        private readonly InvitationRepository $repository,
    ) {
    }

    /**
     * Only the hash is stored, the plain token goes to the email link.
     * Pending invitations for the same email are dropped, so a re-invite invalidates older links.
     *
     * @return array{0: Invitation, 1: string}
     */
    public function run(string $email, int $inviterId): array
    {
        $email = strtolower($email);
        $token = Str::random(64);

        Invitation::query()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        $invitation = $this->repository->create([
            'email' => $email,
            'token_hash' => Invitation::hashToken($token),
            'invited_by' => $inviterId,
            'expires_at' => Date::now()->addDays(config()->integer('appSection-invitation.expires-in-days', 7)),
        ]);

        return [$invitation, $token];
    }
}
