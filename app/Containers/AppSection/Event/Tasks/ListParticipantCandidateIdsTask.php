<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Collection;

final class ListParticipantCandidateIdsTask extends ParentTask
{
    /**
     * Who can be invited: everyone in the event's project, or for a personal event
     * everyone the author shares any project with. The author is included.
     *
     * @return Collection<int, int>
     */
    public function run(int $authorId, int|null $projectId): Collection
    {
        if (null !== $projectId) {
            return Project::query()->findOrFail($projectId)->participantIds();
        }

        return Project::query()
            ->visibleTo($authorId)
            ->with('members:id')
            ->get()
            ->flatMap(static fn (Project $project) => $project->members->pluck('id')->push($project->user_id))
            ->push($authorId)
            ->map(static fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
    }
}
