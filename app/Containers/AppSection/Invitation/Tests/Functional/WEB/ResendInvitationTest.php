<?php

namespace App\Containers\AppSection\Invitation\Tests\Functional\WEB;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Containers\AppSection\Invitation\Notifications\InvitationNotification;
use App\Containers\AppSection\Invitation\Tasks\CreateInvitationTask;
use App\Containers\AppSection\Invitation\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Invitation\UI\WEB\Controllers\ResendInvitationController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ResendInvitationController::class)]
final class ResendInvitationTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function testAdminResendsInvitationWithNewLinkAndExpiry(): void
    {
        $admin = User::factory()->superAdmin()->createOne();
        $invitation = $this->createInvitation('taras@example.com');
        $invitation->update(['expires_at' => now()->addDay()]);
        $this->actingAs($admin, 'web');

        $this->resend($invitation->id)->assertRedirect()->assertSessionHasNoErrors();

        $fresh = Invitation::sole();
        self::assertSame('taras@example.com', $fresh->email);
        self::assertNotSame($invitation->token_hash, $fresh->token_hash);
        self::assertTrue($fresh->expires_at->between(now()->addDays(7)->subMinute(), now()->addDays(7)->addMinute()));

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            InvitationNotification::class,
            static fn (InvitationNotification $notification, array $channels, AnonymousNotifiable $notifiable): bool => 'taras@example.com' === $notifiable->routes['mail']
                && Invitation::hashToken($notification->token) === $fresh->token_hash,
        );
    }

    public function testExpiredInvitationCannotBeResent(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        $invitation = $this->createInvitation('taras@example.com');
        $invitation->update(['expires_at' => now()->subMinute()]);

        $this->resend($invitation->id)->assertNotFound();

        Notification::assertNothingSent();
    }

    public function testAcceptedInvitationCannotBeResent(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        $invitation = $this->createInvitation('taras@example.com');
        $invitation->update(['accepted_at' => now()]);

        $this->resend($invitation->id)->assertNotFound();

        Notification::assertNothingSent();
    }

    public function testRejectsAlreadyRegisteredEmail(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        $invitation = $this->createInvitation('taras@example.com');
        User::factory()->createOne(['email' => 'taras@example.com']);

        $this->resend($invitation->id)->assertSessionHasErrors('email');

        self::assertSame($invitation->token_hash, Invitation::sole()->token_hash);
        Notification::assertNothingSent();
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');
        $invitation = $this->createInvitation('taras@example.com');

        $this->resend($invitation->id)->assertForbidden();

        Notification::assertNothingSent();
    }

    private function createInvitation(string $email): Invitation
    {
        $inviter = User::factory()->superAdmin()->createOne();

        return app(CreateInvitationTask::class)->run($email, $inviter->id)[0];
    }

    private function resend(int $id): TestResponse
    {
        return $this->post(action(ResendInvitationController::class, ['id' => $id]));
    }
}
