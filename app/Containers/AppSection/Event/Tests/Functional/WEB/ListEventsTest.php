<?php

namespace App\Containers\AppSection\Event\Tests\Functional\WEB;

use App\Containers\AppSection\Event\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Event\UI\WEB\Controllers\ListEventsController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListEventsController::class)]
final class ListEventsTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testShowsOwnInvitedAndProjectEventsButNotForeignOnes(): void
    {
        $stranger = User::factory()->createOne();
        $project = $this->createProject($stranger);
        $project->members()->attach($this->user->id, ['role' => ProjectRole::VIEWER->value]);

        $this->createEvent($this->user, ['title' => 'Своя']);
        $this->createEvent($stranger, ['title' => 'Запрошення'])->participants()->attach($this->user->id);
        $this->createEvent($stranger, ['title' => 'Проєктна', 'project_id' => $project->id]);
        $this->createEvent($stranger, ['title' => 'Чужа']);
        $this->createEvent($stranger, ['title' => 'Чужого проєкту', 'project_id' => $this->createProject($stranger)->id]);

        $titles = array_column($this->list()->assertOk()->json('data'), 'title');

        self::assertEqualsCanonicalizing(['Своя', 'Запрошення', 'Проєктна'], $titles);
    }

    public function testReturnsOnlyEventsOverlappingTheRange(): void
    {
        $this->createEvent($this->user, ['title' => 'До', 'start_at' => '2026-09-20 10:00', 'end_at' => '2026-09-20 11:00']);
        $this->createEvent($this->user, ['title' => 'Через межу', 'start_at' => '2026-09-30 22:00', 'end_at' => '2026-10-01 02:00']);
        $this->createEvent($this->user, ['title' => 'Всередині', 'start_at' => '2026-10-15 10:00', 'end_at' => null]);
        $this->createEvent($this->user, ['title' => 'Після', 'start_at' => '2026-11-01 00:00', 'end_at' => '2026-11-01 01:00']);

        $titles = array_column($this->list()->json('data'), 'title');

        self::assertSame(['Через межу', 'Всередині'], $titles);
    }

    public function testFiltersByProject(): void
    {
        $project = $this->createProject($this->user);
        $this->createEvent($this->user, ['title' => 'Проєктна', 'project_id' => $project->id]);
        $this->createEvent($this->user, ['title' => 'Особиста']);

        $titles = array_column($this->list(['search' => 'project_id:' . $project->id])->json('data'), 'title');

        self::assertSame(['Проєктна'], $titles);
    }

    public function testFormatsAllDayEventsAsDatesAndExposesPermissions(): void
    {
        $stranger = User::factory()->createOne();
        $event = $this->createEvent($stranger, [
            'all_day' => true,
            'start_at' => '2026-10-10 00:00:00',
            'end_at' => '2026-10-12 00:00:00',
        ]);
        $event->participants()->attach($this->user->id);

        $this->list()
            ->assertJsonPath('data.0.start_at', '2026-10-10')
            ->assertJsonPath('data.0.end_at', '2026-10-12')
            ->assertJsonPath('data.0.all_day', true)
            ->assertJsonPath('data.0.my_status', 'pending')
            ->assertJsonPath('data.0.permissions.update', false)
            ->assertJsonPath('data.0.participants.data.0.id', $this->user->id);
    }

    public function testProjectAdminCanEditMembersEvents(): void
    {
        $owner = User::factory()->createOne();
        $project = $this->createProject($owner);
        $project->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);
        $this->createEvent($owner, ['project_id' => $project->id]);

        $this->list()->assertJsonPath('data.0.permissions.update', true);
    }

    public function testRequiresRange(): void
    {
        $this->getJson(action(ListEventsController::class))->assertUnprocessable();
    }

    /**
     * @param array<string, string> $query
     */
    private function list(array $query = []): TestResponse
    {
        return $this->getJson(action(ListEventsController::class, [
            'start' => '2026-10-01T00:00:00+00:00',
            'end' => '2026-11-01T00:00:00+00:00',
            ...$query,
        ]));
    }
}
