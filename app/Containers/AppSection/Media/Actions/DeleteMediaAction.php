<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Tasks\DeleteMediaTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteMediaAction extends ParentAction
{
    public function __construct(
        private readonly DeleteMediaTask $deleteTaskTask
    ) {}

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->deleteTaskTask->run($id);
    }
}
