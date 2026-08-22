<?php

namespace App\Containers\AppSection\TimeEntry\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tests\Functional\WebTestCase;
use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\DeleteTimeEntryController;
use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\ListTimeEntriesController;
use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StartTimeEntryController;
use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StopTimeEntryController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StartTimeEntryController::class)]
#[CoversClass(StopTimeEntryController::class)]
#[CoversClass(ListTimeEntriesController::class)]
#[CoversClass(DeleteTimeEntryController::class)]
final class TimeTrackerTest extends WebTestCase
{
    public function testStartRecordsStartedAtToTheSecond(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        Carbon::setTestNow('2026-08-13 10:15:30.987654');

        $this->post(action(StartTimeEntryController::class), [
            'description' => 'Верстка лендінгу',
            'project_id' => $this->createProject()->id,
        ])->assertRedirect();

        $entry = TimeEntry::sole();

        self::assertSame('2026-08-13 10:15:30', $entry->started_at->toDateTimeString());
        self::assertNull($entry->stopped_at);
    }

    public function testStopRecordsStoppedAtAndExactDuration(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        Carbon::setTestNow('2026-08-13 10:15:30');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Код-рев’ю']);

        Carbon::setTestNow('2026-08-13 11:00:05');
        $this->post(action(StopTimeEntryController::class))->assertRedirect();

        $entry = TimeEntry::sole();

        self::assertSame('2026-08-13 11:00:05', $entry->stopped_at->toDateTimeString());
        self::assertSame(2675, $entry->durationInSeconds());
    }

    public function testStartingAnotherTimerStopsThePreviousOne(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Перша']);

        Carbon::setTestNow('2026-08-13 10:30:00');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Друга']);

        self::assertSame(1, TimeEntry::whereNull('stopped_at')->count());
        self::assertSame(1800, TimeEntry::where('description', 'Перша')->sole()->durationInSeconds());
    }

    public function testStartRequiresDescription(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->post(action(StartTimeEntryController::class), ['description' => ''])
            ->assertSessionHasErrors('description');

        self::assertSame(0, TimeEntry::count());
    }

    public function testListsOnlyOwnFinishedEntries(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Своя задача']);
        Carbon::setTestNow('2026-08-13 10:10:00');
        $this->post(action(StopTimeEntryController::class));

        TimeEntry::create([
            'user_id' => User::factory()->createOne()->id,
            'description' => 'Чужа задача',
            'started_at' => Carbon::parse('2026-08-13 09:00:00'),
            'stopped_at' => Carbon::parse('2026-08-13 09:30:00'),
        ]);

        $this->get(action(ListTimeEntriesController::class))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('time-tracker')
                    ->has('entries.data', 1)
                    ->where('entries.data.0.description', 'Своя задача')
                    ->where('entries.data.0.duration', 600)
                    ->where('running', null),
            );
    }

    public function testExposesTheRunningTimerToThePage(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        $project = $this->createProject();

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post(action(StartTimeEntryController::class), [
            'description' => 'Верстка лендінгу',
            'project_id' => $project->id,
        ]);

        $this->get(action(ListTimeEntriesController::class))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('time-tracker')
                    ->has('entries.data', 0)
                    ->where('running.data.description', 'Верстка лендінгу')
                    ->where('running.data.started_at', '2026-08-13T10:00:00+00:00')
                    ->where('running.data.stopped_at', null)
                    ->where('running.data.duration', null)
                    ->where('running.data.project.title', $project->title)
                    ->has('projects', 1)
                    ->has('serverTime'),
            );
    }

    public function testCannotDeleteAnotherUsersEntry(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $foreign = TimeEntry::create([
            'user_id' => User::factory()->createOne()->id,
            'description' => 'Чужа задача',
            'started_at' => Carbon::parse('2026-08-13 09:00:00'),
            'stopped_at' => Carbon::parse('2026-08-13 09:30:00'),
        ]);

        $this->delete(action(DeleteTimeEntryController::class, ['id' => $foreign->id]))->assertRedirect();

        self::assertSame(1, TimeEntry::count());
    }

    private function createProject(): Project
    {
        return Project::create([
            'title' => 'Лендінг',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'description' => 'Тестовий проєкт',
            'budget' => 1000,
        ]);
    }
}
