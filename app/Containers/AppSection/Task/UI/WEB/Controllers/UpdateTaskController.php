<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\UpdateTaskAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\UpdateTaskRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class UpdateTaskController extends WebController
{
    public function __construct(
        private readonly UpdateTaskAction $action
    ) {}

    /**
     * @param UpdateTaskRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(UpdateTaskRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($request, $id);

        return to_route('tasks.index');
    }
}
