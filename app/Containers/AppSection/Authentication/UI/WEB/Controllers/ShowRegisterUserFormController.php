<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowRegisterUserFormController extends WebController
{
    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return Inertia::render('register');
    }
}
