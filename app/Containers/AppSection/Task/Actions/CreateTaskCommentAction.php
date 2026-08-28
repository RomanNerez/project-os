<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tasks\CreateCommentTask;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\UI\WEB\Requests\CreateTaskCommentRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateTaskCommentAction extends ParentAction
{
    public function __construct(
        private readonly CreateCommentTask $createCommentTask
    ) {}

    /**
     * @param CreateTaskCommentRequest $request
     * @return Comment
     */
    public function run(CreateTaskCommentRequest $request): Comment
    {
        return $this->createCommentTask->run([
            'user_id' => $request->user()->id,
            'model_type' => Task::class,
            'model_id' => $request->taskId,
            'body' => $request->body,
        ]);
    }
}
