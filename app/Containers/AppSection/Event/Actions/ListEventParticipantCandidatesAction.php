<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Tasks\ListParticipantCandidateIdsTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListEventParticipantCandidatesAction extends ParentAction
{
    public function __construct(
        private readonly ListParticipantCandidateIdsTask $listParticipantCandidateIdsTask,
    ) {
    }

    /**
     * @return Collection<int, User>
     */
    public function run(int $userId, int|null $projectId): Collection
    {
        $ids = $this->listParticipantCandidateIdsTask->run($userId, $projectId);

        return User::query()->whereKey($ids)->with('media')->get()->sortBy('name')->values();
    }
}
