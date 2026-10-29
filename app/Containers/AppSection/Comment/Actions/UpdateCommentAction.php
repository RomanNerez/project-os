<?php

namespace App\Containers\AppSection\Comment\Actions;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tasks\UpdateCommentTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateCommentAction extends ParentAction
{
    public function __construct(
        private readonly UpdateCommentTask $updateCommentTask
    ) {}

    /**
     * @param int $id
     * @param array<string, mixed> $attributes
     * @return Comment
     */
    public function run(int $id, array $attributes): Comment
    {
        return $this->updateCommentTask->run($id, $attributes);
    }
}
