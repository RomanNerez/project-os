<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\StoreProjectAction;
use App\Containers\AppSection\Project\UI\WEB\Requests\StoreProjectRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class StoreProjectController extends WebController
{
    public function __construct(
        private readonly StoreProjectAction $action
    ) {}

    /**
     * @param StoreProjectRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(StoreProjectRequest $request): Redirector|RedirectResponse
    {
        $this->action->run($request);
        
        return to_route('projects.index');
    }
}
