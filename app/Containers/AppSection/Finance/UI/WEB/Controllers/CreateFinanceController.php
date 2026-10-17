<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use App\Containers\AppSection\Finance\Actions\CreateFinanceAction;
use App\Containers\AppSection\Finance\UI\WEB\Requests\CreateFinanceRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateFinanceController extends WebController
{
    public function __construct(
        private readonly CreateFinanceAction $action,
    ) {
    }

    public function __invoke(CreateFinanceRequest $request): RedirectResponse
    {
        $this->action->run($request, $request->user('web')->id);

        return back();
    }
}
