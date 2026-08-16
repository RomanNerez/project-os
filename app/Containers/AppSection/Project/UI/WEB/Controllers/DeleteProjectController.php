<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\DeleteProjectAction;
use App\Containers\AppSection\Project\UI\WEB\Requests\DeleteProjectRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class DeleteProjectController extends WebController
{
    public function __construct(
        private readonly DeleteProjectAction $action
    ) {}

    /**
     * @param DeleteProjectRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(DeleteProjectRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($id);

        return to_route('projects.index');
    }
}
