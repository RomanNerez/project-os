<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Containers\AppSection\User\UI\WEB\Requests\ShowProfileRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowProfileController extends WebController
{
    public function __invoke(ShowProfileRequest $request): Response
    {
        return Inertia::render('profile', [
            'user' => fractal($request->user('web'), new UserTransformer())->toArray(),
        ]);
    }
}
