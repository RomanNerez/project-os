<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\DeleteTaskAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\DeleteTaskRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class DeleteTaskController extends WebController
{
    public function __construct(
        private readonly DeleteTaskAction $action
    ) {}

    /**
     * @param DeleteTaskRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(DeleteTaskRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($id);

        return to_route('tasks.index');
    }
}
