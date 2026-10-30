<?php

namespace App\Containers\AppSection\Authentication\Tests\Unit\Actions\PasswordReset;

use App\Containers\AppSection\Authentication\Actions\PasswordReset\GenerateUrlAction;
use App\Containers\AppSection\Authentication\Tests\UnitTestCase;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GenerateUrlAction::class)]
final class GenerateUrlActionTest extends UnitTestCase
{
    public function testItCanGenerateCorrectUrl(): void
    {
        $action = new GenerateUrlAction();
        $user = User::factory()->createOne();
        $token = 'token';

        $url = $action($user, $token);

        $this->assertSame(
            route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]),
            $url,
        );
    }
}
