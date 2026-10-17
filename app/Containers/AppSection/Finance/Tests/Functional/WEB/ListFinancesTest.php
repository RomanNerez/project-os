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
    public function testShowsOnlyRecordsForVisibleTasksWithTheirProjects(): void
    {
        $user = User::factory()->createOne();
        $stranger = User::factory()->createOne();
        $this->actingAs($user, 'web');

        $sharedProject = $this->createProject($stranger, 'Спільний');
        $sharedProject->members()->attach($user->id, ['role' => ProjectRole::VIEWER->value]);
        $ownProject = $this->createProject($user, 'Свій');

        $this->createFinance($stranger, 'Видимий', [
            $this->createTask($sharedProject)->id,
            $this->createTask($ownProject)->id,
        ]);
        $this->createFinance($stranger, 'Чужий', [$this->createTask($this->createProject($stranger, 'Чужий'))->id]);

        $this->get(action(ListFinancesController::class))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('finances')
                    ->has('finances.data', 1)
                    ->where('finances.data.0.title', 'Видимий')
                    ->where('finances.data.0.amount_uah', '4150.00')
                    ->has('finances.data.0.projects', 2)
                    ->has('finances.data.0.tasks.data', 2)
                    ->has('finances.data.0.media.data', 0),
            );
    }

    /**
     * @param array<int, int> $taskIds
     */
    private function createFinance(User $author, string $title, array $taskIds): void
    {
        Finance::create([
            'user_id' => $author->id,
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
