<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Carbon\CarbonInterface;

final class StopRunningTimeEntriesTask extends ParentTask
{
    /**
     * Closes every open entry of the user. Guarantees a single running timer
     * even if a stale one was left behind by another tab or device.
     */
    public function run(int $userId, CarbonInterface $stoppedAt): int
    {
        return TimeEntry::query()
            ->where('user_id', $userId)
            ->whereNull('stopped_at')
            ->update(['stopped_at' => $stoppedAt]);
    }
}
