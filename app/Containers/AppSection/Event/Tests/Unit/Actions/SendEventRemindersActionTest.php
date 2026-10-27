<?php

namespace App\Containers\AppSection\Event\Tests\Unit\Actions;

use App\Containers\AppSection\Event\Actions\SendEventRemindersAction;
use App\Containers\AppSection\Event\Notifications\EventReminderNotification;
use App\Containers\AppSection\Event\Tests\UnitTestCase;
use App\Containers\AppSection\User\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SendEventRemindersAction::class)]
final class SendEventRemindersActionTest extends UnitTestCase
{
    public function testRemindsAuthorAndNotDeclinedParticipantsOnce(): void
    {
        Notification::fake();
        $now = CarbonImmutable::parse('2026-10-28 09:50:00');
        [$author, $accepted, $declined] = [User::factory()->createOne(), User::factory()->createOne(), User::factory()->createOne()];

        $due = $this->createEvent($author, ['reminder_minutes' => 15, 'start_at' => '2026-10-28 10:00:00']);
        $due->participants()->attach([$accepted->id => ['status' => 'accepted'], $declined->id => ['status' => 'declined']]);
        $this->createEvent($author, ['title' => 'Ще рано', 'reminder_minutes' => 5, 'start_at' => '2026-10-28 10:00:00']);
        $this->createEvent($author, ['title' => 'Минула', 'reminder_minutes' => 15, 'start_at' => '2026-10-28 09:00:00']);
        $this->createEvent($author, ['title' => 'Без нагадування', 'start_at' => '2026-10-28 10:00:00']);

        $action = app(SendEventRemindersAction::class);

        self::assertSame(1, $action->run($now));
        self::assertSame(0, $action->run($now->addMinute()));

        Notification::assertSentTo([$author, $accepted], EventReminderNotification::class);
        Notification::assertNotSentTo($declined, EventReminderNotification::class);
        Notification::assertCount(2);
        self::assertNotNull($due->refresh()->reminded_at);
    }
}
