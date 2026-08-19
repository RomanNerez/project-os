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
}
