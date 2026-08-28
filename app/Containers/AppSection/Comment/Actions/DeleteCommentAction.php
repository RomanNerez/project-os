<?php

namespace App\Containers\AppSection\Comment\Actions;

use App\Containers\AppSection\Comment\Tasks\DeleteCommentTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteCommentAction extends ParentAction
{
    public function __construct(
        private readonly DeleteCommentTask $deleteTaskTask
    ) {}

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->deleteTaskTask->run($id);
    }
}
