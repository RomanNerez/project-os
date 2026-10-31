<?php

namespace App\Containers\AppSection\User\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Tasks\SaveWorkspaceRoleTask;
use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tests\Functional\WebTestCase;
use App\Containers\AppSection\User\UI\WEB\Controllers\CreateUserController;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateUserController::class)]
final class CreateUserTest extends WebTestCase
{
    public function testAdminCreatesVerifiedUserWithoutRoles(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store([])->assertRedirect()->assertSessionHasNoErrors();

        $user = User::where('email', 'taras@example.com')->sole();

        self::assertSame('Taras Shevchenko', $user->name);
        self::assertNotNull($user->email_verified_at);
        self::assertCount(0, $user->roles);
    }

    public function testAdminCreatesUserWithWorkspaceRole(): void
    {
        $role = app(SaveWorkspaceRoleTask::class)->run(['display_name' => 'Менеджер', 'permissions' => []]);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store(['role_id' => $role->getHashedKey()])->assertRedirect()->assertSessionHasNoErrors();

        self::assertSame([$role->id], User::where('email', 'taras@example.com')->sole()->roles->pluck('id')->all());
    }

    public function testRejectsDuplicateEmail(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        User::factory()->createOne(['email' => 'taras@example.com']);

        $this->store([])->assertSessionHasErrors('email');
    }

    public function testRequiresConfirmedPassword(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store(['password_confirmation' => 'other-password'])->assertSessionHasErrors('password');

        self::assertSame(0, User::where('email', 'taras@example.com')->count());
    }

    public function testRequiresNames(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store(['first_name' => '', 'last_name' => ''])->assertSessionHasErrors(['first_name', 'last_name']);
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->store([])->assertForbidden();

        self::assertSame(0, User::where('email', 'taras@example.com')->count());
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function store(array $overrides): TestResponse
    {
        return $this->post(action(CreateUserController::class), [
            'first_name' => 'Taras',
            'last_name' => 'Shevchenko',
            'email' => 'taras@example.com',
            'password' => 'secret-Password-1',
            'password_confirmation' => 'secret-Password-1',
            ...$overrides,
        ]);
    }
}
