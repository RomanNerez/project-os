<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\TimeEntry\Actions\DeleteTimeEntryAction;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\DeleteTimeEntryRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteTimeEntryController extends WebController
{
    public function __construct(
        private readonly DeleteTimeEntryAction $action,
    ) {
    }

    /**
     * @param DeleteTimeEntryRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function __invoke(DeleteTimeEntryRequest $request, int $id): RedirectResponse
    {
        $this->action->run($id, $request->user('web')->id);

        return back();
    }
}
