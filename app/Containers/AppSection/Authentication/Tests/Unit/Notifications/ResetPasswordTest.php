<?php

namespace App\Containers\AppSection\Authentication\Tests\Unit\Notifications;

use App\Containers\AppSection\Authentication\Actions\PasswordReset\GenerateUrlAction;
use App\Containers\AppSection\Authentication\Notifications\ResetPassword;
use App\Containers\AppSection\Authentication\Tests\UnitTestCase;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ResetPassword::class)]
final class ResetPasswordTest extends UnitTestCase
{
    public function testMailLinksToResetPage(): void
    {
        $user = User::factory()->createOne();

        $mail = (new ResetPassword('token'))->toMail($user);

        $this->assertSame('Скинути пароль', $mail->actionText);
        $this->assertSame((new GenerateUrlAction())($user, 'token'), $mail->actionUrl);
    }

    public function testUserReceivesThisNotificationOnPasswordReset(): void
    {
        Notification::fake();
        $user = User::factory()->createOne();

        $user->sendPasswordResetNotification('token');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
