<?php

namespace App\Containers\AppSection\Authentication\Tests\Unit\Actions\PasswordReset;

use App\Containers\AppSection\Authentication\Actions\PasswordReset\ForgotPasswordAction;
use App\Containers\AppSection\Authentication\Tests\UnitTestCase;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ForgotPasswordAction::class)]
final class ForgotPasswordActionTest extends UnitTestCase
{
    public function testCanSendResetLink(): void
    {
        $user = User::factory()->createOne();
        $result = app(ForgotPasswordAction::class)->run($user->email);

        $this->assertSame(__(Password::RESET_LINK_SENT), $result);
    }

    public function testPassingInvalidDataThrowsException(): void
    {
        $this->expectExceptionObject(
            ValidationException::withMessages([
                'email' => __(Password::INVALID_USER)]),
        );

        app(ForgotPasswordAction::class)->run('non@existing.user');
    }

    public function testItPreventsTooManyRequests(): void
    {
        $this->expectExceptionObject(
            ValidationException::withMessages([
                'throttle' => __(Password::RESET_THROTTLED)]),
        );

        $email = User::factory()->createOne()->email;
        $action = app(ForgotPasswordAction::class);

        $action->run($email);
        $action->run($email);
    }
}
