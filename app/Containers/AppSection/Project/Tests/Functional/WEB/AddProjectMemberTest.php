<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\AddProjectMemberController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddProjectMemberController::class)]
final class AddProjectMemberTest extends WebTestCase
{
    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
        $this->project = Project::create([
            'user_id' => $this->user->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
    }

    public function testStoresHourlyRate(): void
    {
        $member = User::factory()->createOne();

        $this->addMember(['email' => $member->email, 'role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 32.75])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_user', [
            'project_id' => $this->project->id,
            'user_id' => $member->id,
            'role' => ProjectRole::EXECUTOR->value,
            'hourly_rate' => 32.75,
        ]);
    }

    public function testHourlyRateIsOptional(): void
    {
        $member = User::factory()->createOne();

        $this->addMember(['email' => $member->email, 'role' => ProjectRole::CLIENT->value])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_user', [
            'project_id' => $this->project->id,
            'user_id' => $member->id,
            'hourly_rate' => null,
        ]);
    }

    public function testRejectsNegativeHourlyRate(): void
    {
        $member = User::factory()->createOne();

        $this->addMember(['email' => $member->email, 'role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => -1])
            ->assertSessionHasErrors('hourly_rate');

        $this->assertDatabaseMissing('project_user', ['user_id' => $member->id]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function addMember(array $data): TestResponse
    {
        return $this->post(action(AddProjectMemberController::class, ['project' => $this->project->id]), $data);
    }
}
