<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use App\Containers\AppSection\Finance\Actions\CreateFinanceAction;
use App\Containers\AppSection\Finance\UI\WEB\Requests\CreateFinanceRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateFinanceController extends WebController
{
    public function __construct(
        private readonly CreateFinanceAction $action,
    ) {
    }

    public function __invoke(CreateFinanceRequest $request, Project $project): RedirectResponse
    {
        $this->action->run($request, $project->id, $request->user('web')->id);

        return back();
    }
}
