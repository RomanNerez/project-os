<?php

namespace App\Containers\AppSection\Comment\UI\WEB\Controllers;

use App\Containers\AppSection\Comment\Actions\DeleteCommentAction;
use App\Containers\AppSection\Comment\UI\WEB\Requests\DeleteCommentRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class DeleteCommentController extends WebController
{
    public function __construct(
        private readonly DeleteCommentAction $action
    ) {}

    /**
     * @param DeleteCommentRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(DeleteCommentRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($id);

        return back();
    }
}
