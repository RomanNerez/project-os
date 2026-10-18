<?php

namespace App\Containers\AppSection\Finance\Data\Repositories;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Finance
 *
 * @extends ParentRepository<TModel>
 */
final class FinanceRepository extends ParentRepository
{
    protected $fieldSearchable = [
        'title' => 'ilike',
        'project_id' => '=',
        'type' => 'in',
        'status' => 'in',
        'currency' => 'in',
    ];

    /**
     * Records the user created or that belong to a project they participate in.
     *
     * @param int $userId
     * @return self
     */
    public function scopeVisibleForUser(int $userId): self
    {
        $this->scopeQuery(fn ($query) => $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereHas('project', fn ($projectQuery) => $projectQuery->visibleTo($userId));
        }));

        return $this;
    }
}
