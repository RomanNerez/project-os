<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ProjectsController extends WebController
{
    public function showList(): Response
    {
        return Inertia::render('projects');
    }
}
