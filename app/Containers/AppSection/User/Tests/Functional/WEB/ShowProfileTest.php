<?php

namespace App\Containers\AppSection\User\Tests\Functional\WEB;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tests\Functional\WebTestCase;
use App\Containers\AppSection\User\UI\WEB\Controllers\ShowProfileController;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShowProfileController::class)]
final class ShowProfileTest extends WebTestCase
{
    public function testGuestIsRedirected(): void
    {
        $this->get('/profile')->assertRedirect();
    }

    public function testRendersCurrentUserProfile(): void
    {
        $user = User::factory()->createOne(['first_name' => 'Taras', 'last_name' => 'Shevchenko']);

        $this->actingAs($user, 'web')
            ->get('/profile')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('profile')
                ->where('user.data.first_name', 'Taras')
                ->where('user.data.last_name', 'Shevchenko')
                ->where('user.data.name', 'Taras Shevchenko')
                ->where('user.data.avatar_url', null)
                ->where('auth.user.name', 'Taras Shevchenko')
                ->where('auth.user.permissions.manage_users', false));
    }
}
