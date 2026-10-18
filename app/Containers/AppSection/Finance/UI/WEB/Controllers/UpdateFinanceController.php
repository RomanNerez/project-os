<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use App\Containers\AppSection\Finance\Actions\UpdateFinanceAction;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\UI\WEB\Requests\UpdateFinanceRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateFinanceController extends WebController
{
    public function __construct(
        private readonly UpdateFinanceAction $action,
    ) {
    }

    public function __invoke(UpdateFinanceRequest $request, Finance $finance): RedirectResponse
    {
        $this->action->run($request, $finance, $request->user('web')->id);

        return back();
    }
}
