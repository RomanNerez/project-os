<?php

namespace App\Containers\AppSection\Comment\Actions;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tasks\UpdateCommentTask;
use App\Containers\AppSection\Comment\UI\WEB\Requests\UpdateCommentRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateCommentAction extends ParentAction
{
    public function __construct(
        private readonly UpdateCommentTask $deleteTaskTask
    ) {}

    /**
     * @param UpdateCommentRequest $request
     * @return Comment
     */
    public function run(UpdateCommentRequest $request): Comment
    {
        return $this->deleteTaskTask->run($request->id, [
            'body' => $request->body
        ]);
    }
}
