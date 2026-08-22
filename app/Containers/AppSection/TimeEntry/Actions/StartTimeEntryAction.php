<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tasks\CreateTimeEntryTask;
use App\Containers\AppSection\TimeEntry\Tasks\StopRunningTimeEntriesTask;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\StartTimeEntryRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class StartTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly CreateTimeEntryTask $createTimeEntryTask,
        private readonly StopRunningTimeEntriesTask $stopRunningTimeEntriesTask,
    ) {
    }

    public function run(StartTimeEntryRequest $request, int $userId): TimeEntry
    {
        $data = $request->validated();
        $now = now()->startOfSecond();

        return DB::transaction(function () use ($data, $now, $userId): TimeEntry {
            $this->stopRunningTimeEntriesTask->run($userId, $now);

            return $this->createTimeEntryTask->run([
                'user_id' => $userId,
                'project_id' => $data['project_id'] ?? null,
                'description' => $data['description'],
                'started_at' => $now,
                'stopped_at' => null,
            ]);
        });
    }
}
