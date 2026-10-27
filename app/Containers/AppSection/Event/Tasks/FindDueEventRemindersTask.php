<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class FindDueEventRemindersTask extends ParentTask
{
    /**
     * Upcoming events whose reminder time has come and that were not reminded yet.
     *
     * @return Collection<int, Event>
     */
    public function run(CarbonImmutable $now): Collection
    {
        return Event::query()
            ->whereNotNull('reminder_minutes')
            ->whereNull('reminded_at')
            ->where('start_at', '>', $now)
            ->whereRaw("start_at - reminder_minutes * interval '1 minute' <= ?", [$now])
            ->with(['user', 'participants'])
            ->get();
    }
}
