<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tasks\CreateCommentTask;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateTaskCommentAction extends ParentAction
{
    public function __construct(
        private readonly CreateCommentTask $createCommentTask
    ) {}

    /**
     * @param Task $task
     * @param int $userId
     * @param string $body
     * @return Comment
     */
    public function run(Task $task, int $userId, string $body): Comment
    {
        return $this->createCommentTask->run([
            'user_id' => $userId,
            'model_type' => Task::class,
            'model_id' => $task->id,
            'body' => $body,
        ]);
    }
}
