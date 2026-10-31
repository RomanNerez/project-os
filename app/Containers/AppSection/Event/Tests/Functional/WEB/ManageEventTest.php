<?php

namespace App\Containers\AppSection\Event\Tests\Functional\WEB;

use App\Containers\AppSection\Event\Notifications\EventInvitationNotification;
use App\Containers\AppSection\Event\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Event\UI\WEB\Controllers\DeleteEventController;
use App\Containers\AppSection\Event\UI\WEB\Controllers\ListEventParticipantCandidatesController;
use App\Containers\AppSection\Event\UI\WEB\Controllers\MoveEventController;
use App\Containers\AppSection\Event\UI\WEB\Controllers\RespondToEventController;
use App\Containers\AppSection\Event\UI\WEB\Controllers\UpdateEventController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateEventController::class)]
#[CoversClass(MoveEventController::class)]
#[CoversClass(DeleteEventController::class)]
#[CoversClass(RespondToEventController::class)]
#[CoversClass(ListEventParticipantCandidatesController::class)]
final class ManageEventTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testUpdateKeepsAnswersAndInvitesOnlyNewParticipants(): void
    {
        $project = $this->createProject($this->user);
        [$old, $new] = [User::factory()->createOne(), User::factory()->createOne()];
        $project->members()->attach([$old->id, $new->id], ['role' => ProjectRole::EXECUTOR->value]);
        $event = $this->createEvent($this->user, ['project_id' => $project->id]);
        $event->participants()->attach($old->id, ['status' => 'accepted']);

        $this->put(action(UpdateEventController::class, ['event' => $event->id]), [
            'project_id' => $project->id,
            'title' => 'Нова назва',
            'type' => 'deadline',
            'all_day' => false,
            'start_at' => '2026-10-28T10:00:00+00:00',
            'participant_ids' => [$old->id, $new->id],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $event->refresh();

        self::assertSame('Нова назва', $event->title);
        self::assertSame('accepted', $event->participants->firstWhere('id', $old->id)?->pivot->status);
        self::assertSame('pending', $event->participants->firstWhere('id', $new->id)?->pivot->status);
        Notification::assertSentTo($new, EventInvitationNotification::class);
        Notification::assertNotSentTo($old, EventInvitationNotification::class);
    }

    public function testParticipantCannotEditOrDelete(): void
    {
        $event = $this->createEvent(User::factory()->createOne());
        $event->participants()->attach($this->user->id);

        $this->patch(action(MoveEventController::class, ['event' => $event->id]), [
            'all_day' => false,
            'start_at' => '2026-10-29T10:00:00+00:00',
        ])->assertForbidden();
        $this->delete(action(DeleteEventController::class, ['event' => $event->id]))->assertForbidden();
    }

    public function testMoveChangesPeriodAndRearmsReminder(): void
    {
        $event = $this->createEvent($this->user, ['reminder_minutes' => 10, 'reminded_at' => now()]);

        $this->patch(action(MoveEventController::class, ['event' => $event->id]), [
            'all_day' => false,
            'start_at' => '2026-10-29T12:00:00+00:00',
            'end_at' => '2026-10-29T13:00:00+00:00',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $event->refresh();

        self::assertSame('2026-10-29 12:00:00', $event->start_at->toDateTimeString());
        self::assertNull($event->reminded_at);
        self::assertSame('Дзвінок з клієнтом', $event->title);
    }

    public function testProjectOwnerDeletesMembersEvent(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value]);
        $event = $this->createEvent($member, ['project_id' => $project->id]);

        $this->delete(action(DeleteEventController::class, ['event' => $event->id]))->assertRedirect();

        self::assertModelMissing($event);
    }

    public function testParticipantResponds(): void
    {
        $event = $this->createEvent(User::factory()->createOne());
        $event->participants()->attach($this->user->id);

        $this->patch(action(RespondToEventController::class, ['event' => $event->id]), ['status' => 'declined'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        self::assertSame('declined', $event->participants()->sole()->pivot->status);
    }

    public function testNonParticipantCannotRespond(): void
    {
        $event = $this->createEvent(User::factory()->createOne());

        $this->patch(action(RespondToEventController::class, ['event' => $event->id]), ['status' => 'accepted'])
            ->assertForbidden();
    }

    public function testListsProjectParticipantsAsCandidates(): void
    {
        $owner = User::factory()->createOne(['first_name' => 'Олена']);
        $project = $this->createProject($owner);
        $project->members()->attach($this->user->id, ['role' => ProjectRole::EXECUTOR->value]);

        $ids = array_column(
            $this->getJson(action(ListEventParticipantCandidatesController::class, ['project_id' => $project->id]))->assertOk()->json('data'),
            'id',
        );

        self::assertEqualsCanonicalizing([$owner->id, $this->user->id], $ids);
    }

    public function testCannotListCandidatesOfForeignProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());

        $this->getJson(action(ListEventParticipantCandidatesController::class, ['project_id' => $project->id]))
            ->assertForbidden();
    }
}
