<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowForgotPasswordFormController extends WebController
{
    public function __invoke(): Response
    {
        return Inertia::render('forgot-password');
    }
}
