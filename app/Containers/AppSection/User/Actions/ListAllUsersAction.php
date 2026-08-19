<?php

namespace App\Containers\AppSection\User\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tasks\ListAllUsersTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListAllUsersAction extends ParentAction
{
    public function __construct(
        private readonly ListAllUsersTask $listAllUsersTask
    ) {}

    /**
     * @return Collection<int, User>
     */
    public function run(): Collection
    {
        return $this->listAllUsersTask->run();
    }
}
