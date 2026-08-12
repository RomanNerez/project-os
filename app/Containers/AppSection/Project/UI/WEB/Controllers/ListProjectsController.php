<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\UI\WEB\Requests\ListProjectsRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListProjectsController extends WebController
{
    /**
     * @param ListProjectsRequest $request
     * @return Response
     */
    public function __invoke(ListProjectsRequest $request): Response
    {
        return Inertia::render('projects');
    }
}
