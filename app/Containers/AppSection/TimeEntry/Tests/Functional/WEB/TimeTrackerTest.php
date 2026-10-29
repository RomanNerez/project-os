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
use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\UpdateTimeEntryController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StartTimeEntryController::class)]
#[CoversClass(StopTimeEntryController::class)]
#[CoversClass(ListTimeEntriesController::class)]
#[CoversClass(DeleteTimeEntryController::class)]
#[CoversClass(UpdateTimeEntryController::class)]
final class TimeTrackerTest extends WebTestCase
{
    public function testStartRecordsStartedAtToTheSecond(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        Carbon::setTestNow('2026-08-13 10:15:30.987654');

        $this->post($this->startUrl($this->createProject($user)), [
            'description' => 'Верстка лендінгу',
        ])->assertRedirect();

        $entry = TimeEntry::sole();

        self::assertSame('2026-08-13 10:15:30', $entry->started_at->toDateTimeString());
        self::assertNull($entry->stopped_at);
    }

    public function testStopRecordsStoppedAtAndExactDuration(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        Carbon::setTestNow('2026-08-13 10:15:30');
        $this->post($this->startUrl($project), ['description' => 'Код-рев’ю']);

        Carbon::setTestNow('2026-08-13 11:00:05');
        $this->post($this->stopUrl($project))->assertRedirect();

        $entry = TimeEntry::sole();

        self::assertSame('2026-08-13 11:00:05', $entry->stopped_at->toDateTimeString());
        self::assertSame(2675, $entry->durationInSeconds());
    }

    public function testStartingAnotherTimerStopsThePreviousOne(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post($this->startUrl($project), ['description' => 'Перша']);

        Carbon::setTestNow('2026-08-13 10:30:00');
        $this->post($this->startUrl($this->createProject($user)), ['description' => 'Друга']);

        self::assertSame(1, TimeEntry::whereNull('stopped_at')->count());
        self::assertSame(1800, TimeEntry::where('description', 'Перша')->sole()->durationInSeconds());
    }

    public function testStartRequiresDescriptionOrTask(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');

        $this->post($this->startUrl($this->createProject($user)), ['description' => '', 'task_id' => null])
            ->assertSessionHasErrors(['description', 'task_id']);

        self::assertSame(0, TimeEntry::count());
    }

    public function testListsOnlyOwnFinishedEntriesOfTheProject(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        Carbon::setTestNow('2026-08-13 10:00:00');
        $this->post($this->startUrl($project), ['description' => 'Своя задача']);
        Carbon::setTestNow('2026-08-13 10:10:00');
        $this->post($this->stopUrl($project));

        $otherProject = $this->createProject($user);
        Carbon::setTestNow('2026-08-13 11:00:00');
        $this->post($this->startUrl($otherProject), ['description' => 'Інший проєкт']);
        Carbon::setTestNow('2026-08-13 11:10:00');
        $this->post($this->stopUrl($otherProject));

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
        $this->post($this->startUrl($project), ['description' => 'Верстка лендінгу']);

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

    public function testStartWithTaskTakesTitleFromTheTask(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $task = $this->createTask($project, 'Зверстати хедер');

        $this->post($this->startUrl($project), [
            'task_id' => $task->id,
            'description' => '',
        ])->assertSessionHasNoErrors();

        $entry = TimeEntry::sole();

        self::assertSame($task->id, $entry->task_id);
        self::assertSame($project->id, $entry->project_id);
        self::assertSame('Зверстати хедер', $entry->description);
    }

    public function testStartWithTaskKeepsAnExplicitDescription(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $task = $this->createTask($project, 'Зверстати хедер');

        $this->post($this->startUrl($project), [
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

        $this->post($this->startUrl($project), [
            'task_id' => $this->createTask($project, 'Спільна задача')->id,
        ])->assertSessionHasNoErrors();

        self::assertSame(1, TimeEntry::count());
    }

    public function testCannotTrackATaskFromAForeignProject(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $task = $this->createTask($this->createProject(User::factory()->createOne()), 'Чужа задача');

        $this->post($this->startUrl($this->createProject($user)), ['task_id' => $task->id])
            ->assertSessionHasErrors('task_id');

        self::assertSame(0, TimeEntry::count());
    }

    public function testCannotTrackATaskOfAnotherOwnProject(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $task = $this->createTask($this->createProject($user), 'Задача іншого проєкту');

        $this->post($this->startUrl($this->createProject($user)), ['task_id' => $task->id])
            ->assertSessionHasErrors('task_id');

        self::assertSame(0, TimeEntry::count());
    }

    public function testCannotStartTimerInAForeignProject(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->post($this->startUrl($this->createProject(User::factory()->createOne())), ['description' => 'Чужий проєкт'])
            ->assertForbidden();

        self::assertSame(0, TimeEntry::count());
    }

    public function testExposesTheTrackedTaskToThePage(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $task = $this->createTask($project, 'Зверстати хедер');

        $this->post($this->startUrl($project), ['task_id' => $task->id]);

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
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        $foreign = $this->createEntry(User::factory()->createOne(), $project);

        $this->delete($this->destroyUrl($project, $foreign))->assertRedirect();

        self::assertSame(1, TimeEntry::count());
    }

    public function testDeletesOwnEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);

        $this->delete($this->destroyUrl($project, $this->createEntry($user, $project)))->assertRedirect();

        self::assertSame(0, TimeEntry::count());
    }

    public function testCannotDeleteAnEntryOfAnotherProjectThroughThisOne(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $this->createProject($user));

        $this->delete($this->destroyUrl($project, $entry))->assertRedirect();

        self::assertSame(1, TimeEntry::count());
    }

    public function testUpdatesTheTimeOfOwnEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $project);

        $this->patch($this->updateUrl($project, $entry), [
            'started_at' => '2026-08-12T14:00:00Z',
            'stopped_at' => '2026-08-12T15:45:00Z',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $entry->refresh();

        self::assertSame('2026-08-12 14:00:00', $entry->started_at->toDateTimeString());
        self::assertSame('2026-08-12 15:45:00', $entry->stopped_at->toDateTimeString());
        self::assertSame(6300, $entry->durationInSeconds());
    }

    public function testRenamesAFreeFormEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $project);

        $this->patch($this->updateUrl($project, $entry), [
            'description' => 'Нова назва',
            'started_at' => '2026-08-13T09:00:00Z',
            'stopped_at' => '2026-08-13T09:30:00Z',
        ])->assertSessionHasNoErrors();

        self::assertSame('Нова назва', $entry->refresh()->description);
    }

    public function testKeepsTheTaskTitleOfATaskEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $task = $this->createTask($project, 'Зверстати хедер');
        $entry = $this->createEntry($user, $project);
        $entry->update(['task_id' => $task->id, 'description' => $task->title]);

        $this->patch($this->updateUrl($project, $entry), [
            'description' => 'Інша назва',
            'started_at' => '2026-08-13T09:00:00Z',
            'stopped_at' => '2026-08-13T10:00:00Z',
        ])->assertSessionHasNoErrors();

        $entry->refresh();

        self::assertSame('Зверстати хедер', $entry->description);
        self::assertSame(3600, $entry->durationInSeconds());
    }

    public function testUpdateRequiresStopAfterStart(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $project);

        $this->patch($this->updateUrl($project, $entry), [
            'started_at' => '2026-08-13T10:00:00Z',
            'stopped_at' => '2026-08-13T09:00:00Z',
        ])->assertSessionHasErrors('stopped_at');

        self::assertSame(1800, $entry->refresh()->durationInSeconds());
    }

    public function testCannotUpdateAnotherUsersEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $foreign = $this->createEntry(User::factory()->createOne(), $project);

        $this->patch($this->updateUrl($project, $foreign), $this->updatePayload())->assertNotFound();

        self::assertSame(1800, $foreign->refresh()->durationInSeconds());
    }

    public function testCannotUpdateAnEntryOfAnotherProjectThroughThisOne(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $this->createProject($user));

        $this->patch($this->updateUrl($project, $entry), $this->updatePayload())->assertNotFound();

        self::assertSame(1800, $entry->refresh()->durationInSeconds());
    }

    public function testCannotUpdateARunningEntry(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');
        $project = $this->createProject($user);
        $entry = $this->createEntry($user, $project);
        $entry->update(['stopped_at' => null]);

        $this->patch($this->updateUrl($project, $entry), $this->updatePayload())->assertNotFound();

        self::assertNull($entry->refresh()->stopped_at);
    }

    private function updateUrl(Project $project, TimeEntry $entry): string
    {
        return action(UpdateTimeEntryController::class, ['project' => $project->id, 'id' => $entry->id]);
    }

    /**
     * @return array<string, string>
     */
    private function updatePayload(): array
    {
        return [
            'started_at' => '2026-08-13T08:00:00Z',
            'stopped_at' => '2026-08-13T12:00:00Z',
        ];
    }

    private function startUrl(Project $project): string
    {
        return action(StartTimeEntryController::class, ['project' => $project->id]);
    }

    private function stopUrl(Project $project): string
    {
        return action(StopTimeEntryController::class, ['project' => $project->id]);
    }

    private function destroyUrl(Project $project, TimeEntry $entry): string
    {
        return action(DeleteTimeEntryController::class, ['project' => $project->id, 'id' => $entry->id]);
    }

    private function createEntry(User $user, Project $project): TimeEntry
    {
        return TimeEntry::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'description' => 'Запис',
            'started_at' => Carbon::parse('2026-08-13 09:00:00'),
            'stopped_at' => Carbon::parse('2026-08-13 09:30:00'),
        ]);
    }

    private function createProject(User|null $owner = null): Project
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
