<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\CreateTaskAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\CreateTaskRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class CreateTaskController extends WebController
{
    public function __construct(
        private readonly CreateTaskAction $action
    ) {}

    /**
     * @param CreateTaskRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(CreateTaskRequest $request): Redirector|RedirectResponse
    {
        $this->action->run($request);

        return to_route('tasks.index');
    }
}
