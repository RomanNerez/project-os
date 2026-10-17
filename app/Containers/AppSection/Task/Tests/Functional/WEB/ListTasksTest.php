<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\ListTasksController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListTasksController::class)]
final class ListTasksTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testCreatableProjectsContainOnlyProjectsWhereUserCanCreateTasks(): void
    {
        $this->createProject('Власний', $this->user);
        $this->createProject('Адмін', User::factory()->createOne())
            ->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);
        $this->createProject('Учасник', User::factory()->createOne())
            ->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);
        $this->createProject('Спостерігач', User::factory()->createOne())
            ->members()->attach($this->user->id, ['role' => ProjectRole::VIEWER->value]);
        $this->createProject('Чужий', User::factory()->createOne());

        $response = $this->get(action(ListTasksController::class));
        $response->assertOk();

        self::assertSame(
            ['Адмін', 'Власний', 'Учасник'],
            array_column($response->viewData('page')['props']['creatableProjects'], 'title'),
        );
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
}
