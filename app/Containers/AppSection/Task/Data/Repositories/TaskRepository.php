<?php

namespace App\Containers\AppSection\Task\Data\Repositories;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Task
 *
 * @extends ParentRepository<TModel>
 */
final class TaskRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];

    /**
     * @param int $userId
     * @return self
     */
    public function scopeVisibleForUser(int $userId): self
    {
        $this->scopeQuery(fn($query) => $query->where(function ($q) use ($userId) {
            $q->whereHas('project', function ($projectQuery) use ($userId) {
                $projectQuery->where('user_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId) {
                        $memberQuery->where('users.id', $userId);
                    });
            })

            ->orWhere(function ($noProjectQuery) use ($userId) {
                $noProjectQuery->whereNull('project_id')
                    ->where(function ($userQuery) use ($userId) {
                        $userQuery->where('user_id', $userId)
                                ->orWhere('assignee_id', $userId);
                    });
            });
        }));

        return $this;
    }
}
