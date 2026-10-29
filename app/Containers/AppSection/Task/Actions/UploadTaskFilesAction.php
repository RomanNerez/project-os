<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UploadTaskFilesAction extends ParentAction
{
    /**
     * @param Task $task
     * @return void
     */
    public function run(Task $task): void
    {
        $task
            ->addMultipleMediaFromRequest(['files'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
    }
}
