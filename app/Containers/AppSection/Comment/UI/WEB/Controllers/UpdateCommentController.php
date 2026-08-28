<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Controllers;

use App\Containers\AppSection\Comment\Actions\UpdateCommentAction;
use App\Containers\AppSection\Comment\UI\WEB\Requests\UpdateCommentRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class UpdateCommentController extends WebController
{
    public function __construct(
        private readonly UpdateCommentAction $action
    ) {}

    /**
     * @param UpdateCommentRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(UpdateCommentRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($request);

        return back();
    }
}
