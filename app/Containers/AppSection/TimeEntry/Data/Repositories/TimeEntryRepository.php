<?php

namespace App\Containers\AppSection\TimeEntry\Data\Repositories;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of TimeEntry
 *
 * @extends ParentRepository<TModel>
 */
final class TimeEntryRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
