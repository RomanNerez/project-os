<?php

namespace App\Containers\AppSection\Authentication\Tests\Functional\WEB;

use App\Containers\AppSection\Authentication\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\LoginController;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ResetPasswordController;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowResetPasswordFormController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ResetPasswordController::class)]
#[CoversClass(ShowResetPasswordFormController::class)]
final class ResetPasswordTest extends WebTestCase
{
    private const NEW_PASSWORD = 'NewSecret#2026';

    public function testFormReceivesTokenAndEmail(): void
    {
        $this->get(action(ShowResetPasswordFormController::class, ['token' => 'abc', 'email' => 'taras@example.com']))
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('reset-password')
                ->where('token', 'abc')
                ->where('email', 'taras@example.com'));
    }

    public function testResetsPasswordWithValidToken(): void
    {
        $user = User::factory()->createOne();
        $token = Password::createToken($user);

        $this->post(action(ResetPasswordController::class), [
            'token' => $token,
            'email' => $user->email,
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ])
            ->assertRedirect(action([LoginController::class, 'showForm']))
            ->assertSessionHasNoErrors();

        self::assertTrue(Hash::check(self::NEW_PASSWORD, $user->refresh()->password));
    }

    public function testRejectsInvalidToken(): void
    {
        $user = User::factory()->createOne();

        $this->post(action(ResetPasswordController::class), [
            'token' => 'invalid',
            'email' => $user->email,
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ])->assertSessionHasErrors('token');

        self::assertFalse(Hash::check(self::NEW_PASSWORD, $user->refresh()->password));
    }

    public function testRequiresConfirmedPassword(): void
    {
        $user = User::factory()->createOne();

        $this->post(action(ResetPasswordController::class), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');
    }
}
