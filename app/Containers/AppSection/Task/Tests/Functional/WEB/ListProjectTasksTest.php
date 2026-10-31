<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\ListProjectTasksController;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListProjectTasksController::class)]
final class ListProjectTasksTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testListsOnlyTasksOfTheProject(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $this->createTask($project, 'Зверстати хедер');
        $this->createTask($this->createProject('Інший', $this->user), 'Задача іншого проєкту');

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('tasks')
                    ->has('tasks.data', 1)
                    ->where('tasks.data.0.title', 'Зверстати хедер')
                    ->where('project.data.id', $project->id)
                    ->where('project.data.permissions.create_task', true),
            );
    }

    public function testIncludesFinishedTimeEntriesOfTheTask(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $task = $this->createTask($project, 'Зверстати хедер');
        $this->createTimeEntry($task, '2026-10-28 10:00:00', '2026-10-28 11:30:00');
        $this->createTimeEntry($task, '2026-10-28 12:00:00', null);

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->has('tasks.data.0.time_entries.data', 1)
                    ->where('tasks.data.0.time_entries.data.0.duration', 5400)
                    ->where('tasks.data.0.time_entries.data.0.user.data.name', $this->user->name),
            );
    }

    public function testIncludesHistoryOfTheTask(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $task = $this->createTask($project, 'Зверстати хедер');
        $task->refresh()->update(['title' => 'Зверстати футер']);

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->has('tasks.data.0.activities.data', 2)
                    ->where('tasks.data.0.activities.data.1.event', 'created')
                    ->where('tasks.data.0.activities.data.1.changes', [])
                    ->where('tasks.data.0.activities.data.1.causer.data.name', $this->user->name)
                    ->where('tasks.data.0.activities.data.0.event', 'updated')
                    ->where('tasks.data.0.activities.data.0.changes', [
                        ['field' => 'title', 'old' => 'Зверстати хедер', 'new' => 'Зверстати футер'],
                    ])
                    ->where('tasks.data.0.activities.data.0.causer.data.name', $this->user->name),
            );
    }

    public function testFiltersByTitleWithinTheProject(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $this->createTask($project, 'Зверстати хедер');
        $this->createTask($project, 'Налаштувати CI');

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id, 'search' => 'title:хедер', 'searchJoin' => 'and']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('tasks.data', 1)->where('tasks.data.0.title', 'Зверстати хедер'));
    }

    public function testViewerCannotCreateTasks(): void
    {
        $project = $this->createProject('Клієнт', User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => ProjectRole::CLIENT->value]);

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('project.data.permissions.create_task', false));
    }

    public function testCannotOpenTasksOfAForeignProject(): void
    {
        $project = $this->createProject('Чужий', User::factory()->createOne());

        $this->get(action(ListProjectTasksController::class, ['id' => $project->id]))->assertForbidden();
    }

    private function createProject(string $title, User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => $title,
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
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

    private function createTimeEntry(Task $task, string $startedAt, ?string $stoppedAt): TimeEntry
    {
        return TimeEntry::create([
            'user_id' => $this->user->id,
            'project_id' => $task->project_id,
            'task_id' => $task->id,
            'description' => $task->title,
            'started_at' => $startedAt,
            'stopped_at' => $stoppedAt,
        ]);
    }
}
