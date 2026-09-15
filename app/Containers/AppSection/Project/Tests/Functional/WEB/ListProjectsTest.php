<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\ListProjectsController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListProjectsController::class)]
final class ListProjectsTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testListsOwnAndMemberProjectsWithoutFilters(): void
    {
        $this->createProject(['title' => 'Власний проєкт'], $this->user);
        $this->createMemberProject(['title' => 'Проєкт-членство']);
        $this->createProject(['title' => 'Чужий проєкт'], User::factory()->createOne());

        self::assertSame(['Власний проєкт', 'Проєкт-членство'], $this->listedTitles());
    }

    public function testFiltersByPartialTitleMatch(): void
    {
        $this->createProject(['title' => 'Редизайн сайту'], $this->user);
        $this->createProject(['title' => 'Сайт для клініки'], $this->user);
        $this->createProject(['title' => 'Мобільний застосунок'], $this->user);

        self::assertSame(
            ['Редизайн сайту', 'Сайт для клініки'],
            $this->listedTitles($this->filters('сайт')),
        );
    }

    public function testTitleFilterIgnoresCase(): void
    {
        $this->createProject(['title' => 'Редизайн сайту'], $this->user);

        self::assertSame(['Редизайн сайту'], $this->listedTitles($this->filters('САЙТ')));
    }

    public function testTitleFilterFindsMatchBeyondTheFirstPage(): void
    {
        foreach (range(1, 12) as $number) {
            $this->createProject(['title' => "Проєкт {$number}"], $this->user);
        }
        $this->createProject(['title' => 'Редизайн сайту'], $this->user);

        self::assertSame(['Редизайн сайту'], $this->listedTitles($this->filters('сайт')));
    }

    public function testTitleFilterIgnoresDescription(): void
    {
        $this->createProject([
            'title' => 'Мобільний застосунок',
            'description' => 'Переносимо контент із сайту',
        ], $this->user);

        self::assertSame([], $this->listedTitles($this->filters('сайт')));
    }

    public function testFiltersBySingleStatus(): void
    {
        $this->createProject(['title' => 'У процесі', 'status' => ProjectStatus::IN_PROGRESS->value], $this->user);
        $this->createProject(['title' => 'Чернетка', 'status' => ProjectStatus::DRAFT->value], $this->user);

        self::assertSame(['У процесі'], $this->listedTitles($this->filters(statuses: ['in_progress'])));
    }

    public function testFiltersBySeveralStatusesAtOnce(): void
    {
        $this->createProject(['title' => 'Чернетка', 'status' => ProjectStatus::DRAFT->value], $this->user);
        $this->createProject(['title' => 'На паузі', 'status' => ProjectStatus::ON_HOLD->value], $this->user);
        $this->createProject(['title' => 'Завершено', 'status' => ProjectStatus::COMPLETED->value], $this->user);

        self::assertSame(
            ['На паузі', 'Чернетка'],
            $this->listedTitles($this->filters(statuses: ['draft', 'on_hold'])),
        );
    }

    public function testUnknownStatusYieldsEmptyListWithoutError(): void
    {
        $this->createProject(['title' => 'Чернетка', 'status' => ProjectStatus::DRAFT->value], $this->user);

        self::assertSame([], $this->listedTitles($this->filters(statuses: ['archived'])));
    }

    public function testUnknownStatusNextToKnownOneKeepsTheKnownOne(): void
    {
        $this->createProject(['title' => 'Чернетка', 'status' => ProjectStatus::DRAFT->value], $this->user);

        self::assertSame(['Чернетка'], $this->listedTitles($this->filters(statuses: ['archived', 'draft'])));
    }

    public function testCombinesTitleAndStatusWithAndLogic(): void
    {
        $this->createProject(['title' => 'Сайт клініки', 'status' => ProjectStatus::IN_PROGRESS->value], $this->user);
        $this->createProject(['title' => 'Сайт школи', 'status' => ProjectStatus::DRAFT->value], $this->user);
        $this->createProject(['title' => 'Застосунок', 'status' => ProjectStatus::IN_PROGRESS->value], $this->user);

        self::assertSame(
            ['Сайт клініки'],
            $this->listedTitles($this->filters('сайт', ['in_progress'])),
        );
    }

    public function testWithoutSearchJoinTheSameQueryReturnsAWiderResult(): void
    {
        $this->createProject(['title' => 'Сайт клініки', 'status' => ProjectStatus::IN_PROGRESS->value], $this->user);
        $this->createProject(['title' => 'Сайт школи', 'status' => ProjectStatus::DRAFT->value], $this->user);
        $this->createProject(['title' => 'Застосунок', 'status' => ProjectStatus::IN_PROGRESS->value], $this->user);

        self::assertSame(
            ['Застосунок', 'Сайт клініки', 'Сайт школи'],
            $this->listedTitles(['search' => 'title:сайт;status:in_progress']),
        );
    }

    public function testForeignProjectStaysHiddenUnderFilters(): void
    {
        $this->createProject([
            'title' => 'Сайт конкурента',
            'status' => ProjectStatus::IN_PROGRESS->value,
        ], User::factory()->createOne());

        self::assertSame([], $this->listedTitles($this->filters('сайт', ['in_progress'])));
    }

    public function testMemberProjectMatchesTheStatusFilter(): void
    {
        $this->createMemberProject(['title' => 'Проєкт-членство', 'status' => ProjectStatus::DRAFT->value]);

        self::assertSame(['Проєкт-членство'], $this->listedTitles($this->filters(statuses: ['draft'])));
    }

    public function testMemberProjectIsFilteredOutByStatus(): void
    {
        $this->createMemberProject(['title' => 'Проєкт-членство', 'status' => ProjectStatus::COMPLETED->value]);

        self::assertSame([], $this->listedTitles($this->filters(statuses: ['draft'])));
    }

    public function testRejectsSearchLongerThanTheAllowedLength(): void
    {
        $this->get(action(ListProjectsController::class, ['search' => str_repeat('a', 513)]))
            ->assertSessionHasErrors('search');
    }

    /**
     * @param array<int, string> $statuses
     * @return array<string, string>
     */
    private function filters(string $title = '', array $statuses = []): array
    {
        $parts = [];

        if ('' !== $title) {
            $parts[] = "title:{$title}";
        }

        if ([] !== $statuses) {
            $parts[] = 'status:' . implode(',', $statuses);
        }

        return [
            'search' => implode(';', $parts),
            'searchJoin' => 'and',
        ];
    }

    /**
     * @param array<string, mixed> $query
     * @return array<int, string>
     */
    private function listedTitles(array $query = []): array
    {
        $response = $this->get(action(ListProjectsController::class, $query));
        $response->assertOk();

        $titles = array_column($response->viewData('page')['props']['projects']['data'], 'title');
        sort($titles);

        return $titles;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createProject(array $attributes, User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
            ...$attributes,
        ]);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createMemberProject(array $attributes): Project
    {
        $project = $this->createProject($attributes, User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);

        return $project;
    }
}
