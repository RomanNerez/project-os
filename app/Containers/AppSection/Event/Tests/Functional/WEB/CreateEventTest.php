<?php

namespace App\Containers\AppSection\Event\Tests\Functional\WEB;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Notifications\EventInvitationNotification;
use App\Containers\AppSection\Event\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Event\UI\WEB\Controllers\CreateEventController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateEventController::class)]
final class CreateEventTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testCreatesPersonalEventStoringTimesInUtc(): void
    {
        $this->store([])->assertRedirect()->assertSessionHasNoErrors();

        $event = Event::sole();

        self::assertSame($this->user->id, $event->user_id);
        self::assertNull($event->project_id);
        self::assertSame('2026-10-28 07:00:00', $event->start_at->toDateTimeString());
        self::assertSame('2026-10-28 08:30:00', $event->end_at?->toDateTimeString());
        self::assertSame(15, $event->reminder_minutes);
    }

    public function testStoresAllDayEventAsDates(): void
    {
        $this->store(['all_day' => true, 'start_at' => '2026-10-28', 'end_at' => '2026-10-30'])
            ->assertSessionHasNoErrors();

        $event = Event::sole();

        self::assertTrue($event->all_day);
        self::assertSame('2026-10-28 00:00:00', $event->start_at->toDateTimeString());
        self::assertSame('2026-10-30 00:00:00', $event->end_at?->toDateTimeString());
    }

    public function testInvitesProjectParticipants(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::MEMBER->value]);

        $this->store(['project_id' => $project->id, 'participant_ids' => [$member->id, $this->user->id]])
            ->assertSessionHasNoErrors();

        $event = Event::sole();

        self::assertSame($project->id, $event->project_id);
        self::assertSame([$member->id], $event->participants->pluck('id')->all());
        self::assertSame('pending', $event->participants->first()?->pivot->status);
        Notification::assertSentTo($member, EventInvitationNotification::class);
        Notification::assertNotSentTo($this->user, EventInvitationNotification::class);
    }

    public function testInvitesColleaguesToPersonalEvent(): void
    {
        $colleague = User::factory()->createOne();
        $this->createProject($colleague)->members()->attach($this->user->id, ['role' => ProjectRole::VIEWER->value]);

        $this->store(['participant_ids' => [$colleague->id]])->assertSessionHasNoErrors();

        self::assertSame([$colleague->id], Event::sole()->participants->pluck('id')->all());
    }

    public function testRejectsParticipantOutsideTheProject(): void
    {
        $project = $this->createProject($this->user);
        $outsider = User::factory()->createOne();

        $this->store(['project_id' => $project->id, 'participant_ids' => [$outsider->id]])
            ->assertSessionHasErrors('participant_ids');

        self::assertSame(0, Event::count());
        Notification::assertNothingSent();
    }

    public function testRejectsStrangerInPersonalEvent(): void
    {
        $this->store(['participant_ids' => [User::factory()->createOne()->id]])->assertSessionHasErrors('participant_ids');

        self::assertSame(0, Event::count());
    }

    public function testRejectsProjectTheUserDoesNotParticipateIn(): void
    {
        $foreign = $this->createProject(User::factory()->createOne());

        $this->store(['project_id' => $foreign->id])->assertSessionHasErrors('project_id');

        self::assertSame(0, Event::count());
    }

    public function testRejectsEndBeforeStart(): void
    {
        $this->store(['end_at' => '2026-10-28T09:00:00+03:00'])->assertSessionHasErrors('end_at');

        self::assertSame(0, Event::count());
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function store(array $overrides): TestResponse
    {
        return $this->post(action(CreateEventController::class), [
            'title' => 'Планування спринту',
            'type' => 'meeting',
            'all_day' => false,
            'start_at' => '2026-10-28T10:00:00+03:00',
            'end_at' => '2026-10-28T11:30:00+03:00',
            'reminder_minutes' => 15,
            ...$overrides,
        ]);
    }
}
