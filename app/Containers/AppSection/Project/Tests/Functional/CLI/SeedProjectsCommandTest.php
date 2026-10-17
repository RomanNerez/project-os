<?php

namespace App\Containers\AppSection\Project\Tests\Functional\CLI;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\FunctionalTestCase;
use App\Containers\AppSection\Project\UI\CLI\Commands\SeedProjectsCommand;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SeedProjectsCommand::class)]
final class SeedProjectsCommandTest extends FunctionalTestCase
{
    public function testCreatesProjectsWithOwnerAndMembers(): void
    {
        $owner = User::factory()->createOne();

        $this->artisan('seed:projects', ['--count' => 2, '--owner' => $owner->id, '--members' => 3])
            ->assertSuccessful();

        $projects = Project::query()->with('members')->get();
        $this->assertCount(2, $projects);

        foreach ($projects as $project) {
            $this->assertSame($owner->id, $project->user_id);
            $this->assertCount(3, $project->members);
            $this->assertNotContains($owner->id, $project->members->pluck('id'));
        }
    }

    public function testCanRunRepeatedly(): void
    {
        $this->artisan('seed:projects', ['--count' => 1])->assertSuccessful();
        $this->artisan('seed:projects', ['--count' => 1])->assertSuccessful();

        $this->assertSame(2, Project::query()->count());
    }

    public function testFailsForUnknownOwner(): void
    {
        $this->artisan('seed:projects', ['--owner' => 999999])->assertFailed();

        $this->assertSame(0, Project::query()->count());
    }
}
