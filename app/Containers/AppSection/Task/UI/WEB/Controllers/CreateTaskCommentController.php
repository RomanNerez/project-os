<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\CreateTaskCommentAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\CreateTaskCommentRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class CreateTaskCommentController extends WebController
{
    public function __construct(
        private readonly CreateTaskCommentAction $action
    ) {}

    /**
     * @param CreateTaskCommentRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(CreateTaskCommentRequest $request): Redirector|RedirectResponse
    {
        $this->action->run($request);

        return back();
    }
}
