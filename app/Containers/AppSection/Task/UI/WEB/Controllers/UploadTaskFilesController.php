<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\UploadTaskFilesAction;
use App\Containers\AppSection\Task\UI\WEB\Requests\UploadTaskFilesRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UploadTaskFilesController extends WebController
{
    public function __construct(
        private readonly UploadTaskFilesAction $action
    ) {}

    /**
     * @param UploadTaskFilesRequest $request
     * @return RedirectResponse
     */
    public function __invoke(UploadTaskFilesRequest $request, int $id): RedirectResponse
    {
        $this->action->run($request, $id);

        return back();
    }
}
