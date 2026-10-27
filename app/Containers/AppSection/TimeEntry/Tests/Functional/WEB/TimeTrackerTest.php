<?php

namespace App\Containers\AppSection\TimeEntry\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
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

    public function testStartRequiresDescriptionOrTask(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->post(action(StartTimeEntryController::class), ['description' => '', 'task_id' => null])
            ->assertSessionHasErrors(['description', 'task_id']);

        self::assertSame(0, TimeEntry::count());
    }

    public function testListsOnlyOwnFinishedEntriesOfTheProject(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Своя задача', 'project_id' => $project->id]);
        Carbon::setTestNow('2026-08-13 10:10:00');
        $this->post(action(StopTimeEntryController::class));

        Carbon::setTestNow('2026-08-13 11:00:00');
        $this->post(action(StartTimeEntryController::class), ['description' => 'Інший проєкт', 'project_id' => $this->createProject($user)->id]);
        Carbon::setTestNow('2026-08-13 11:10:00');
        $this->post(action(StopTimeEntryController::class));

        TimeEntry::create([
            'user_id' => User::factory()->createOne()->id,
            'project_id' => $project->id,
            'description' => 'Чужа задача',
            'started_at' => Carbon::parse('2026-08-13 09:00:00'),
            'stopped_at' => Carbon::parse('2026-08-13 09:30:00'),
        ]);

        $this->get(action(ListTimeEntriesController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('time-tracker')
                    ->has('entries.data', 1)
                    ->where('entries.data.0.description', 'Своя задача')
                    ->where('entries.data.0.duration', 600)
                    ->where('running', null)
                    ->where('project.data.id', $project->id),
            );
    }

    public function testCannotOpenTheTrackerOfAForeignProject(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        $project = $this->createProject(User::factory()->createOne());

        $this->get(action(ListTimeEntriesController::class, ['id' => $project->id]))->assertForbidden();
    }

    public function testExposesTheRunningTimerToThePage(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post(action(StartTimeEntryController::class), [
            'description' => 'Верстка лендінгу',
            'project_id' => $project->id,
        ]);

        $this->get(action(ListTimeEntriesController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('time-tracker')
                    ->has('entries.data', 0)
                    ->where('running.data.description', 'Верстка лендінгу')
                    ->where('running.data.started_at', '2026-08-13T10:00:00+00:00')
                    ->where('running.data.stopped_at', null)
                    ->where('running.data.duration', null)
                    ->where('running.data.project.title', $project->title)
                    ->missing('projects')
                    ->has('serverTime'),
            );
    }

    public function testStartWithTaskTakesProjectAndTitleFromTheTask(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $task = $this->createTask($this->createProject($user), 'Зверстати хедер');

        $this->post(action(StartTimeEntryController::class), [
            'task_id' => $task->id,
            'description' => '',
            'project_id' => $this->createProject($user)->id,
        ])->assertSessionHasNoErrors();

        $entry = TimeEntry::sole();

        self::assertSame($task->id, $entry->task_id);
        self::assertSame($task->project_id, $entry->project_id);
        self::assertSame('Зверстати хедер', $entry->description);
    }

    public function testStartWithTaskKeepsAnExplicitDescription(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $task = $this->createTask($this->createProject($user), 'Зверстати хедер');

        $this->post(action(StartTimeEntryController::class), [
            'task_id' => $task->id,
            'description' => 'Мобільна версія хедера',
        ])->assertSessionHasNoErrors();

        self::assertSame('Мобільна версія хедера', TimeEntry::sole()->description);
    }

    public function testCanTrackATaskOfAProjectUserIsMemberOf(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($user->id, ['role' => ProjectRole::MEMBER->value]);

        $this->post(action(StartTimeEntryController::class), [
            'task_id' => $this->createTask($project, 'Спільна задача')->id,
        ])->assertSessionHasNoErrors();

        self::assertSame(1, TimeEntry::count());
    }

    public function testCannotTrackATaskFromAForeignProject(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        $task = $this->createTask($this->createProject(User::factory()->createOne()), 'Чужа задача');

        $this->post(action(StartTimeEntryController::class), ['task_id' => $task->id])
            ->assertSessionHasErrors('task_id');

        self::assertSame(0, TimeEntry::count());
    }

    public function testExposesTheTrackedTaskToThePage(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $task = $this->createTask($this->createProject($user), 'Зверстати хедер');

        $this->post(action(StartTimeEntryController::class), ['task_id' => $task->id]);

        $this->get(action(ListTimeEntriesController::class, ['id' => $task->project_id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('time-tracker')
                    ->where('running.data.task.id', $task->id)
                    ->where('running.data.task.title', 'Зверстати хедер'),
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

    private function createProject(?User $owner = null): Project
    {
        return Project::create([
            'user_id' => $owner?->id,
            'title' => 'Лендінг',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'description' => 'Тестовий проєкт',
            'budget' => 1000,
        ]);
    }

    private function createTask(Project $project, string $title): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => $title,
        ]);
    }
}
