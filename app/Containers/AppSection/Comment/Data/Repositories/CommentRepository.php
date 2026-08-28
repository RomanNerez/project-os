<?php

namespace App\Containers\AppSection\Comment\Data\Repositories;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Comment
 *
 * @extends ParentRepository<TModel>
 */
final class CommentRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
