<?php

namespace App\Containers\AppSection\Event\Tests\Functional\WEB;

use App\Containers\AppSection\Event\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Event\UI\WEB\Controllers\ShowCalendarController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShowCalendarController::class)]
final class ShowCalendarTest extends WebTestCase
{
    public function testRendersCalendarPage(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->get(action(ShowCalendarController::class))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('calendar'));
    }

    public function testRedirectsGuests(): void
    {
        $this->get(action(ShowCalendarController::class))->assertRedirect();
    }
}
