<?php

namespace App\Containers\AppSection\Notification\Tests\Functional\WEB;

use App\Containers\AppSection\Notification\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Notification\UI\WEB\Controllers\ListNotificationsController;
use App\Containers\AppSection\Notification\UI\WEB\Controllers\MarkAllNotificationsAsReadController;
use App\Containers\AppSection\Notification\UI\WEB\Controllers\MarkNotificationAsReadController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListNotificationsController::class)]
#[CoversClass(MarkNotificationAsReadController::class)]
#[CoversClass(MarkAllNotificationsAsReadController::class)]
final class NotificationsTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testListsOnlyOwnNotificationsNewestFirst(): void
    {
        $old = $this->createNotification($this->user, ['title' => 'Стара'], read: true);
        $old->forceFill(['created_at' => now()->subDay()])->save();
        $new = $this->createNotification($this->user, ['title' => 'Нова']);
        $this->createNotification(User::factory()->createOne(), ['title' => 'Чужа']);

        $this->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $new->id)
            ->assertJsonPath('data.0.title', 'Нова')
            ->assertJsonPath('data.0.read_at', null)
            ->assertJsonPath('data.1.id', $old->id)
            ->assertJsonPath('meta.unread_count', 1);
    }

    public function testPaginatesNotifications(): void
    {
        for ($i = 0; $i < 16; ++$i) {
            $this->createNotification($this->user);
        }

        $this->getJson(route('notifications.index', ['page' => 2]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.unread_count', 16);
    }

    public function testMarksOwnNotificationAsRead(): void
    {
        $notification = $this->createNotification($this->user);
        $this->createNotification($this->user);

        $this->patchJson(route('notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->assertNotNull($notification->fresh()?->read_at);
    }

    public function testCannotMarkForeignNotificationAsRead(): void
    {
        $foreign = $this->createNotification(User::factory()->createOne());

        $this->patchJson(route('notifications.read', $foreign->id))->assertNotFound();

        $this->assertNull($foreign->fresh()?->read_at);
    }

    public function testMarksAllOwnNotificationsAsRead(): void
    {
        $this->createNotification($this->user);
        $this->createNotification($this->user);
        $foreign = $this->createNotification(User::factory()->createOne());

        $this->patchJson(route('notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $this->user->unreadNotifications()->count());
        $this->assertNull($foreign->fresh()?->read_at);
    }

    public function testSharesUnreadCountWithPages(): void
    {
        $this->createNotification($this->user);

        $this->get(route('calendar.index'))
            ->assertInertia(static fn ($page) => $page->where('auth.user.unread_notifications_count', 1));
    }

    public function testGuestCannotListNotifications(): void
    {
        auth('web')->logout();

        $this->getJson(route('notifications.index'))->assertUnauthorized();
    }
}
