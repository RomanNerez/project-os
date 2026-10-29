<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Controllers;

use App\Containers\AppSection\Comment\Actions\DeleteCommentAction;
use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\UI\WEB\Requests\DeleteProjectTaskCommentRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteProjectTaskCommentController extends WebController
{
    public function __construct(
        private readonly DeleteCommentAction $action
    ) {}

    /**
     * @param DeleteProjectTaskCommentRequest $request
     * @param Project $project
     * @param Task $task
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function __invoke(DeleteProjectTaskCommentRequest $request, Project $project, Task $task, Comment $comment): RedirectResponse
    {
        $this->action->run($comment->id);

        return back();
    }
}
