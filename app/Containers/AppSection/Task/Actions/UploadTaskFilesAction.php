<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Tasks\FindTaskByIdTask;
use App\Containers\AppSection\Task\UI\WEB\Requests\UploadTaskFilesRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UploadTaskFilesAction extends ParentAction
{
    public function __construct(
        private readonly FindTaskByIdTask $findTaskByIdTask
    ) {}

    /**
     * @param UploadTaskFilesRequest $request
     * @param int $id
     * @return void
     */
    public function run(UploadTaskFilesRequest $request, int $id): void
    {
        $task = $this->findTaskByIdTask->run($id);

        $task
            ->addMultipleMediaFromRequest(['files'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
    }
}
