<?php

namespace App\Containers\AppSection\Authentication\Tests\Functional\WEB;

use App\Containers\AppSection\Authentication\Notifications\ResetPassword;
use App\Containers\AppSection\Authentication\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ForgotPasswordController;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowForgotPasswordFormController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ForgotPasswordController::class)]
#[CoversClass(ShowForgotPasswordFormController::class)]
final class ForgotPasswordTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    public function testFormIsRendered(): void
    {
        $this->get(action(ShowForgotPasswordFormController::class))
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page->component('forgot-password'));
    }

    public function testSendsResetLinkToExistingUser(): void
    {
        $user = User::factory()->createOne();

        $this->from(action(ShowForgotPasswordFormController::class))
            ->post(action(ForgotPasswordController::class), ['email' => $user->email])
            ->assertRedirect(action(ShowForgotPasswordFormController::class))
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function testDoesNotRevealUnknownEmail(): void
    {
        $this->from(action(ShowForgotPasswordFormController::class))
            ->post(action(ForgotPasswordController::class), ['email' => 'nobody@example.com'])
            ->assertRedirect(action(ShowForgotPasswordFormController::class))
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function testRequiresValidEmail(): void
    {
        $this->post(action(ForgotPasswordController::class), ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    public function testThrottlesRepeatedRequests(): void
    {
        $user = User::factory()->createOne();

        $this->post(action(ForgotPasswordController::class), ['email' => $user->email]);

        $this->post(action(ForgotPasswordController::class), ['email' => $user->email])
            ->assertSessionHasErrors('email');
    }

    public function testAuthenticatedUserCannotAccessForm(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web')
            ->get(action(ShowForgotPasswordFormController::class))
            ->assertRedirect();
    }
}
