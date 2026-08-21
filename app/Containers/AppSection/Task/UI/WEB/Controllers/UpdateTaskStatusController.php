<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\UpdateTaskStatusAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\UpdateTaskStatusRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateTaskStatusController extends WebController
{
    public function __construct(
        private readonly UpdateTaskStatusAction $action
    ) {}

    /**
     * @param UpdateTaskStatusRequest $request
     * @return RedirectResponse
     */
    public function __invoke(UpdateTaskStatusRequest $request, int $id): RedirectResponse
    {
        $this->action->run($request, $id);

        return back();
    }
}
