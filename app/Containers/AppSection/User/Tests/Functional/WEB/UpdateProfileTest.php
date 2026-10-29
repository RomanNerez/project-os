<?php

namespace App\Containers\AppSection\User\Tests\Functional\WEB;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tests\Functional\WebTestCase;
use App\Containers\AppSection\User\UI\WEB\Controllers\UpdateProfileController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateProfileController::class)]
final class UpdateProfileTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne(['first_name' => 'Old', 'last_name' => 'Name']);
        $this->actingAs($this->user, 'web');
    }

    public function testUpdatesNames(): void
    {
        $this->update()->assertRedirect()->assertSessionHasNoErrors();

        $this->user->refresh();

        self::assertSame('Lesya', $this->user->first_name);
        self::assertSame('Ukrainka', $this->user->last_name);
        self::assertSame('Lesya Ukrainka', $this->user->name);
    }

    public function testUploadsAndReplacesAvatar(): void
    {
        $this->update(['avatar' => UploadedFile::fake()->image('a.png')])->assertSessionHasNoErrors();
        $this->update(['avatar' => UploadedFile::fake()->image('b.jpg')])->assertSessionHasNoErrors();

        $media = $this->user->refresh()->getMedia(User::AVATAR_COLLECTION);

        self::assertCount(1, $media);
        self::assertSame('b.jpg', $media->first()->file_name);
        self::assertNotNull($this->user->getAvatarUrl());
    }

    public function testReplacesAvatarWithSameFileNameKeepingOneFile(): void
    {
        $this->update(['avatar' => UploadedFile::fake()->image('avatar.png')])->assertSessionHasNoErrors();
        $this->update(['avatar' => UploadedFile::fake()->image('avatar.png')])->assertSessionHasNoErrors();

        $media = $this->user->refresh()->getMedia(User::AVATAR_COLLECTION);
        $directory = 'users/' . $this->user->id . '/avatar';

        self::assertCount(1, $media);
        self::assertEqualsCanonicalizing(
            [$directory . '/avatar.png', $directory . '/conversions/avatar-thumb.jpg'],
            Storage::disk('public')->allFiles($directory),
        );
    }

    public function testRemovesAvatar(): void
    {
        $this->update(['avatar' => UploadedFile::fake()->image('a.png')]);

        $this->update(['remove_avatar' => true])->assertSessionHasNoErrors();

        self::assertCount(0, $this->user->refresh()->getMedia(User::AVATAR_COLLECTION));
        self::assertNull($this->user->getAvatarUrl());
    }

    public function testValidatesInput(): void
    {
        $this->update([
            'first_name' => '',
            'last_name' => 'x',
            'avatar' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors(['first_name', 'last_name', 'avatar']);

        self::assertSame('Old Name', $this->user->refresh()->name);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function update(array $overrides = []): TestResponse
    {
        return $this->post('/profile', [
            'first_name' => 'Lesya',
            'last_name' => 'Ukrainka',
            ...$overrides,
        ]);
    }
}
