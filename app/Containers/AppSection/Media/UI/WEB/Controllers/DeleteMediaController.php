<?php

namespace App\Containers\AppSection\Media\UI\WEB\Controllers;

use App\Containers\AppSection\Media\Actions\DeleteMediaAction;
use App\Containers\AppSection\Media\UI\WEB\Requests\DeleteMediaRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class DeleteMediaController extends WebController
{
    public function __construct(
        private readonly DeleteMediaAction $action
    ) {}

    /**
     * @param DeleteMediaRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(DeleteMediaRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($id);

        return back();
    }
}
