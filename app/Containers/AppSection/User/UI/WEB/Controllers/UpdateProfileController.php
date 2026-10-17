<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\User\Actions\UpdateProfileAction;
use App\Containers\AppSection\User\UI\WEB\Requests\UpdateProfileRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateProfileController extends WebController
{
    public function __construct(
        private readonly UpdateProfileAction $action,
    ) {
    }

    public function __invoke(UpdateProfileRequest $request): RedirectResponse
    {
        $this->action->run($request, $request->user('web')->id);

        return back();
    }
}
