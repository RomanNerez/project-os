<?php

namespace App\Containers\AppSection\Task\Tests\Functional\CLI;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\FunctionalTestCase;
use App\Containers\AppSection\Task\UI\CLI\Commands\SeedTasksCommand;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SeedTasksCommand::class)]
final class SeedTasksCommandTest extends FunctionalTestCase
{
    public function testCreatesTasksOnlyInGivenProject(): void
    {
        $project = Project::factory()->createOne();
        $project->members()->attach(User::factory()->createOne()->id, ['role' => ProjectRole::MEMBER->value]);
        $otherProject = Project::factory()->createOne();

        $this->artisan('seed:tasks', ['project' => $project->id, '--count' => 30])->assertSuccessful();

        $this->assertSame(30, $project->tasks()->count());
        $this->assertSame(0, $otherProject->tasks()->count());

        $participantIds = $project->participantIds();
        Task::query()->each(function (Task $task) use ($participantIds) {
            $this->assertContains($task->user_id, $participantIds);
            $this->assertTrue(null === $task->assignee_id || $participantIds->contains($task->assignee_id));
        });
    }

    public function testFailsForUnknownProject(): void
    {
        $this->artisan('seed:tasks', ['project' => 999999])->assertFailed();
    }
}
