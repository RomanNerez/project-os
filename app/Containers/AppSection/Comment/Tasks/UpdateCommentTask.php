<?php

namespace App\Containers\AppSection\Comment\Tasks;

use App\Containers\AppSection\Comment\Data\Repositories\CommentRepository;
use App\Containers\AppSection\Comment\Models\Comment;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateCommentTask extends ParentTask
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    /**
     * @param int $id
     * @param array<string, mixed> $attributes
     * @return Comment
     */
    public function run(int $id, array $attributes): Comment
    {
        return $this->repository->update($attributes, $id);
    }
}
