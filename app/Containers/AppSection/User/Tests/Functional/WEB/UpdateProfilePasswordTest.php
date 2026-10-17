<?php

namespace App\Containers\AppSection\User\Tests\Functional\WEB;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tests\Functional\WebTestCase;
use App\Containers\AppSection\User\UI\WEB\Controllers\UpdateProfilePasswordController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateProfilePasswordController::class)]
final class UpdateProfilePasswordTest extends WebTestCase
{
    private const OLD = 'Old-pa$$word1';
    private const NEW = 'New-pa$$word2';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->createOne(['password' => self::OLD]);
        $this->actingAs($this->user, 'web');
    }

    public function testChangesPassword(): void
    {
        $this->change()->assertRedirect()->assertSessionHasNoErrors();

        self::assertTrue(Hash::check(self::NEW, $this->user->refresh()->password));
    }

    public function testRejectsWrongCurrentPassword(): void
    {
        $this->change(['current_password' => 'wrong'])->assertSessionHasErrors('current_password');

        self::assertTrue(Hash::check(self::OLD, $this->user->refresh()->password));
    }

    public function testRejectsMismatchedConfirmation(): void
    {
        $this->change(['new_password_confirmation' => 'Other-pa$$word3'])->assertSessionHasErrors('new_password_confirmation');

        self::assertTrue(Hash::check(self::OLD, $this->user->refresh()->password));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function change(array $overrides = []): TestResponse
    {
        return $this->put('/profile/password', [
            'current_password' => self::OLD,
            'new_password' => self::NEW,
            'new_password_confirmation' => self::NEW,
            ...$overrides,
        ]);
    }
}
