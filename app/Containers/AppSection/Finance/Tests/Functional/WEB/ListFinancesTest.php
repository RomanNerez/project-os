<?php

namespace App\Containers\AppSection\Finance\Tests\Functional\WEB;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Finance\UI\WEB\Controllers\ListFinancesController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListFinancesController::class)]
final class ListFinancesTest extends WebTestCase
{
    public function testShowsOnlyRecordsOfTheProject(): void
    {
        $user = User::factory()->createOne();
        $stranger = User::factory()->createOne();
        $this->actingAs($user, 'web');

        $sharedProject = $this->createProject($stranger, 'Спільний');
        $sharedProject->members()->attach($user->id, ['role' => ProjectRole::VIEWER->value]);

        $this->createFinance($stranger, $sharedProject, 'Видимий', [
            $this->createTask($sharedProject)->id,
            $this->createTask($sharedProject)->id,
        ]);
        $otherProject = $this->createProject($stranger, 'Інший');
        $otherProject->members()->attach($user->id, ['role' => ProjectRole::VIEWER->value]);
        $this->createFinance($stranger, $otherProject, 'Іншого проєкту');

        $this->get(action(ListFinancesController::class, ['id' => $sharedProject->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('finances')
                    ->has('finances.data', 1)
                    ->where('finances.data.0.title', 'Видимий')
                    ->where('finances.data.0.amount_uah', '4150.00')
                    ->where('finances.data.0.project.title', 'Спільний')
                    ->where('finances.data.0.permissions.update', false)
                    ->has('finances.data.0.tasks.data', 2)
                    ->has('finances.data.0.media.data', 0)
                    ->where('project.data.id', $sharedProject->id),
            );
    }

    public function testCannotOpenFinancesOfAForeignProject(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        $project = $this->createProject(User::factory()->createOne(), 'Чужий');

        $this->get(action(ListFinancesController::class, ['id' => $project->id]))->assertForbidden();
    }

    public function testAuthorCanUpdateOwnRecord(): void
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'web');

        $project = $this->createProject($user, 'Свій');
        $this->createFinance($user, $project, 'Свій');

        $this->get(action(ListFinancesController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('finances.data.0.permissions.update', true));
    }

    /**
     * @param array<int, int> $taskIds
     */
    private function createFinance(User $author, Project $project, string $title, array $taskIds = []): void
    {
        Finance::create([
            'user_id' => $author->id,
            'project_id' => $project->id,
            'title' => $title,
            'type' => 'expense',
            'currency' => 'usd',
            'amount' => 100,
            'exchange_rate' => 41.5,
            'amount_uah' => 4150,
            'status' => 'paid',
        ])->tasks()->sync($taskIds);
    }

    private function createProject(User $owner, string $title): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => $title,
            'status' => ProjectStatus::IN_PROGRESS->value,
            'description' => 'Тестовий проєкт',
            'budget' => 1000,
        ]);
    }

    private function createTask(Project $project): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => 'Задача',
        ]);
    }
}
