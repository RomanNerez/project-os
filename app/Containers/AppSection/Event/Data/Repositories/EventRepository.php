<?php

namespace App\Containers\AppSection\Event\Data\Repositories;

use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Repositories\Repository as ParentRepository;
use Carbon\CarbonImmutable;

/**
 * @template TModel of Event
 *
 * @extends ParentRepository<TModel>
 */
final class EventRepository extends ParentRepository
{
    protected $fieldSearchable = [
        'project_id' => '=',
        'type' => 'in',
    ];

    /**
     * Visible events overlapping the range; one method because `scopeQuery` keeps a single closure.
     */
    public function scopeVisibleForUserBetween(int $userId, CarbonImmutable $start, CarbonImmutable $end): self
    {
        $this->scopeQuery(fn ($query) => $query->visibleTo($userId)->overlapping($start, $end));

        return $this;
    }
}
