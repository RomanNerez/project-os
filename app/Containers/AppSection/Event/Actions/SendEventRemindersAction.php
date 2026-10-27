<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Enums\EventParticipantStatus;
use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Notifications\EventReminderNotification;
use App\Containers\AppSection\Event\Tasks\FindDueEventRemindersTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;

final class SendEventRemindersAction extends ParentAction
{
    public function __construct(
        private readonly FindDueEventRemindersTask $findDueEventRemindersTask,
    ) {
    }

    /**
     * Reminds the author and every participant who has not declined; each event once.
     *
     * @return int number of reminded events
     */
    public function run(CarbonImmutable|null $now = null): int
    {
        $now ??= CarbonImmutable::now();
        $events = $this->findDueEventRemindersTask->run($now);

        $events->each(static function (Event $event) use ($now): void {
            $recipients = $event->participants
                ->reject(static fn (User $user): bool => EventParticipantStatus::DECLINED->value === $user->pivot->status)
                ->prepend($event->user);

            Notification::send($recipients, new EventReminderNotification($event));

            $event->forceFill(['reminded_at' => $now])->save();
        });

        return $events->count();
    }
}
