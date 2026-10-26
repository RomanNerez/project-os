<?php

namespace App\Containers\AppSection\Authentication\Tests\Functional\WEB;

use App\Containers\AppSection\Authentication\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\RegisterUserController;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowRegisterUserFormController;
use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\Invitation\Tasks\CreateInvitationTask;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegisterUserController::class)]
#[CoversClass(ShowRegisterUserFormController::class)]
final class RegisterUserTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function testFormReceivesInvitationForValidToken(): void
    {
        $token = $this->createInvitation('taras@example.com');

        $this->get(action(ShowRegisterUserFormController::class, ['token' => $token]))
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('register')
                ->where('invitation.email', 'taras@example.com')
                ->where('invitation.token', $token));
    }

    public function testFormHasNoInvitationWithoutToken(): void
    {
        $this->get(action(ShowRegisterUserFormController::class))
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('register')
                ->where('invitation', null));
    }

    public function testRegistersWithInvitationEmailAndConsumesToken(): void
    {
        $token = $this->createInvitation('taras@example.com');

        $this->register($token, ['email' => 'hacker@example.com'])
            ->assertRedirect(route('projects.index'))
            ->assertSessionHasNoErrors();

        $user = User::where('email', 'taras@example.com')->sole();
        self::assertNotNull($user->email_verified_at);
        self::assertSame(0, User::where('email', 'hacker@example.com')->count());
        self::assertNotNull(Invitation::sole()->accepted_at);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function testTokenCannotBeReused(): void
    {
        $token = $this->createInvitation('taras@example.com');
        $this->register($token);
        auth('web')->logout();

        $this->register($token, ['first_name' => 'Other'])->assertSessionHasErrors('token');

        self::assertSame(1, User::where('email', 'taras@example.com')->count());
        $this->get(action(ShowRegisterUserFormController::class, ['token' => $token]))
            ->assertInertia(static fn (AssertableInertia $page) => $page->where('invitation', null));
    }

    public function testRejectsExpiredToken(): void
    {
        $token = $this->createInvitation('taras@example.com');
        Invitation::query()->update(['expires_at' => now()->subMinute()]);

        $this->register($token)->assertSessionHasErrors('token');

        self::assertSame(0, User::where('email', 'taras@example.com')->count());
    }

    public function testRejectsUnknownOrMissingToken(): void
    {
        $this->register('unknown-token')->assertSessionHasErrors('token');
        $this->register(null)->assertSessionHasErrors('token');

        self::assertSame(0, User::where('email', 'taras@example.com')->count());
    }

    private function createInvitation(string $email): string
    {
        [, $token] = app(CreateInvitationTask::class)->run($email, User::factory()->superAdmin()->createOne()->id);

        return $token;
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function register(string|null $token, array $overrides = []): TestResponse
    {
        return $this->post(action(RegisterUserController::class), [
            'token' => $token,
            'first_name' => 'Taras',
            'last_name' => 'Shevchenko',
            'password' => 'secret-Password-1',
            'password_confirmation' => 'secret-Password-1',
            'gender' => 'male',
            'birth' => '1990-01-01',
            ...$overrides,
        ]);
    }
}
