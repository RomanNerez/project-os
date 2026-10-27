<?php

namespace App\Containers\AppSection\Invitation\Tasks;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Date;

final class ListPendingInvitationsTask extends ParentTask
{
    /**
     * Not accepted, not expired, and the email has not been registered some other way (e.g. created by an admin).
     *
     * @return Collection<int, Invitation>
     */
    public function run(): Collection
    {
        return Invitation::query()
            ->with('inviter')
            ->whereNull('accepted_at')
            ->where('expires_at', '>', Date::now())
            ->whereNotExists(static fn (Builder $query) => $query
                ->selectRaw('1')
                ->from('users')
                ->whereColumn('users.email', 'invitations.email'))
            ->orderByDesc('created_at')
            ->get();
    }
}
