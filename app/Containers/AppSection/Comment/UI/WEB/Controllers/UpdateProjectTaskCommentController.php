<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Controllers;

use App\Containers\AppSection\Comment\Actions\UpdateCommentAction;
use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\UI\WEB\Requests\UpdateProjectTaskCommentRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateProjectTaskCommentController extends WebController
{
    public function __construct(
        private readonly UpdateCommentAction $action
    ) {}

    /**
     * @param UpdateProjectTaskCommentRequest $request
     * @param Project $project
     * @param Task $task
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function __invoke(UpdateProjectTaskCommentRequest $request, Project $project, Task $task, Comment $comment): RedirectResponse
    {
        $this->action->run($comment->id, $request->validated());

        return back();
    }
}
