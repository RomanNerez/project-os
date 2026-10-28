<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\ListProjectTaskOptionsController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListProjectTaskOptionsController::class)]
final class ListProjectTaskOptionsTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testReturnsOnlyTasksOfTheProjectAsJson(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $this->createTask($project, 'Зверстати хедер');
        $this->createTask($this->createProject('Інший', $this->user), 'Задача іншого проєкту');

        $this->getJson(action(ListProjectTaskOptionsController::class, ['id' => $project->id]))
            ->assertOk()
            ->assertHeaderMissing('X-Inertia')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Зверстати хедер')
            ->assertJsonPath('data.0.project.data.title', 'Власний')
            ->assertJsonMissingPath('data.0.comments');
    }

    public function testFiltersByTitleWithinTheProject(): void
    {
        $project = $this->createProject('Власний', $this->user);
        $this->createTask($project, 'Зверстати хедер');
        $this->createTask($project, 'Налаштувати CI');

        $this->getJson(action(ListProjectTaskOptionsController::class, [
            'id' => $project->id,
            'search' => 'title:хедер',
            'searchJoin' => 'and',
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Зверстати хедер');
    }

    public function testCannotListTasksOfAForeignProject(): void
    {
        $project = $this->createProject('Чужий', User::factory()->createOne());
        $this->createTask($project, 'Чужа задача');

        $this->getJson(action(ListProjectTaskOptionsController::class, ['id' => $project->id]))
            ->assertForbidden();
    }

    public function testReturnsNotFoundForUnknownProject(): void
    {
        $this->getJson(action(ListProjectTaskOptionsController::class, ['id' => 999999]))
            ->assertNotFound();
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
}
