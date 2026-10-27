<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateEventTask extends ParentTask
{
    /**
     * A new time or reminder offset re-arms the reminder. Participants keep their answers;
     * `$participantIds` null leaves them as they are.
     *
     * @param array<string, mixed> $data
     * @param array<int, int>|null $participantIds
     *
     * @return array<int, int> ids of newly invited participants
     */
    public function run(Event $event, array $data, array|null $participantIds = null): array
    {
        $event->fill($data);

        if ($event->isDirty(['start_at', 'reminder_minutes'])) {
            $event->reminded_at = null;
        }

        $event->save();

        if (null === $participantIds) {
            return [];
        }

        /** @var array{attached: array<int, int>} $changes */
        $changes = $event->participants()->sync($participantIds);

        return array_map('intval', $changes['attached']);
    }
}
