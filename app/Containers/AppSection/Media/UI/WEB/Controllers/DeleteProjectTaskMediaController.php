<?php

namespace App\Containers\AppSection\Media\UI\WEB\Controllers;

use App\Containers\AppSection\Media\Actions\DeleteMediaAction;
use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\UI\WEB\Requests\DeleteProjectTaskMediaRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteProjectTaskMediaController extends WebController
{
    public function __construct(
        private readonly DeleteMediaAction $action
    ) {}

    /**
     * @param DeleteProjectTaskMediaRequest $request
     * @param Project $project
     * @param Task $task
     * @param Media $media
     * @return RedirectResponse
     */
    public function __invoke(DeleteProjectTaskMediaRequest $request, Project $project, Task $task, Media $media): RedirectResponse
    {
        $this->action->run($media->id);

        return back();
    }
}
