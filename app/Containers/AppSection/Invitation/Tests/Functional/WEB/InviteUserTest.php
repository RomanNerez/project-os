<?php

namespace App\Containers\AppSection\Invitation\Tests\Functional\WEB;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\Invitation\Notifications\InvitationNotification;
use App\Containers\AppSection\Invitation\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Invitation\UI\WEB\Controllers\InviteUserController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InviteUserController::class)]
final class InviteUserTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function testAdminInvitesUserByEmail(): void
    {
        $admin = User::factory()->superAdmin()->createOne();
        $this->actingAs($admin, 'web');

        $this->invite('Taras@Example.com')->assertRedirect()->assertSessionHasNoErrors();

        $invitation = Invitation::sole();
        self::assertSame('taras@example.com', $invitation->email);
        self::assertSame($admin->id, $invitation->invited_by);
        self::assertNull($invitation->accepted_at);
        self::assertTrue($invitation->expires_at->between(now()->addDays(7)->subMinute(), now()->addDays(7)->addMinute()));

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            InvitationNotification::class,
            static fn (InvitationNotification $notification, array $channels, AnonymousNotifiable $notifiable): bool => 'taras@example.com' === $notifiable->routes['mail']
                && Invitation::hashToken($notification->token) === $invitation->token_hash,
        );
    }

    public function testReinviteInvalidatesPreviousLink(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->invite('taras@example.com');
        $firstHash = Invitation::sole()->token_hash;

        $this->invite('taras@example.com');

        self::assertSame(1, Invitation::count());
        self::assertNotSame($firstHash, Invitation::sole()->token_hash);
    }

    public function testRejectsAlreadyRegisteredEmail(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        User::factory()->createOne(['email' => 'taras@example.com']);

        $this->invite('taras@example.com')->assertSessionHasErrors('email');

        self::assertSame(0, Invitation::count());
        Notification::assertNothingSent();
    }

    public function testRequiresValidEmail(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->invite('not-an-email')->assertSessionHasErrors('email');
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->invite('taras@example.com')->assertForbidden();

        self::assertSame(0, Invitation::count());
    }

    private function invite(string $email): TestResponse
    {
        return $this->post(action(InviteUserController::class), ['email' => $email]);
    }
}
