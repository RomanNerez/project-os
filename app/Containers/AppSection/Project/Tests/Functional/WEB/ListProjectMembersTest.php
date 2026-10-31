<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\ListProjectMembersController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(ListProjectMembersController::class)]
final class ListProjectMembersTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testOwnerSeesProjectMembers(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::ADMIN->value]);

        $this->listMembers($project->id)
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('project-members')
                    ->where('project.data.id', $project->id)
                    ->has('project.data.members.data', 1)
                    ->where('project.data.members.data.0.id', $member->id)
                    ->where('project.data.members.data.0.role', ProjectRole::ADMIN->value)
                    ->where('project.data.permissions.manage_members', true),
            );
    }

    public function testMembersIncludeAvatarUrl(): void
    {
        Storage::fake('public');

        $project = $this->createProject($this->user);
        $withAvatar = User::factory()->createOne();
        $withAvatar->addMedia(UploadedFile::fake()->image('avatar.png'))->toMediaCollection(User::AVATAR_COLLECTION);
        $withoutAvatar = User::factory()->createOne();
        $project->members()->attach($withAvatar->id, ['role' => ProjectRole::EXECUTOR->value]);
        $project->members()->attach($withoutAvatar->id, ['role' => ProjectRole::EXECUTOR->value]);

        $this->listMembers($project->id)
            ->assertOk()
            ->assertInertia(function ($page) use ($withAvatar, $withoutAvatar): void {
                $members = collect($page->toArray()['props']['project']['data']['members']['data'])->keyBy('id');

                self::assertSame($withAvatar->getAvatarUrl(), $members[$withAvatar->id]['avatar_url']);
                self::assertNotNull($members[$withAvatar->id]['avatar_url']);
                self::assertNull($members[$withoutAvatar->id]['avatar_url']);
            });
    }

    /**
     * @return array<string, array{ProjectRole}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN],
            'executor' => [ProjectRole::EXECUTOR],
            'client' => [ProjectRole::CLIENT],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberWithAnyRoleSeesMembers(ProjectRole $role): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);

        $this->listMembers($project->id)->assertOk();
    }

    public function testOutsiderCannotSeeMembers(): void
    {
        $project = $this->createProject(User::factory()->createOne());

        $this->listMembers($project->id)->assertForbidden();
    }

    public function testReturnsNotFoundForMissingProject(): void
    {
        $this->listMembers(999_999)->assertNotFound();
    }

    private function listMembers(int $id): TestResponse
    {
        return $this->get(action(ListProjectMembersController::class, ['id' => $id]));
    }

    private function createProject(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
    }
}
