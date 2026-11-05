<?php

namespace App\Containers\AppSection\Finance\Tests\Functional\WEB;

use App\Containers\AppSection\Finance\Enums\FinanceStatus;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Finance\UI\WEB\Controllers\ListFinanceTasksController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListFinanceTasksController::class)]
final class ListFinanceTasksTest extends WebTestCase
{
    private const HOUR = 3600;

    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->project = $this->createProject($this->user);
        $this->actingAs($this->user, 'web');
    }

    public function testReturnsUnpaidRemainderPerUserWithMemberRate(): void
    {
        $member = User::factory()->createOne();
        $this->project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 20]);
        $task = $this->createTask($this->project, 'Верстка');

        $this->track($task, $member, 2 * self::HOUR);
        $this->track($task, $member, self::HOUR);
        $this->track($task, $this->user, self::HOUR);
        $this->pay($task, $member, 2 * self::HOUR);

        $data = $this->list()->assertOk()->json('data');

        self::assertCount(1, $data);
        self::assertSame(['id' => $task->id, 'title' => 'Верстка', 'project_id' => $this->project->id], $data[0]['task']);

        $users = collect($data[0]['users'])->keyBy('user.id');

        self::assertSame(3 * self::HOUR, $users[$member->id]['tracked_seconds']);
        self::assertSame(2 * self::HOUR, $users[$member->id]['paid_seconds']);
        self::assertEquals(20, $users[$member->id]['hourly_rate']);
        self::assertSame(self::HOUR, $users[$this->user->id]['tracked_seconds']);
        self::assertSame(0, $users[$this->user->id]['paid_seconds']);
        self::assertNull($users[$this->user->id]['hourly_rate']);
    }

    public function testSkipsFullyPaidTasksAndUsers(): void
    {
        $paid = $this->createTask($this->project);
        $partly = $this->createTask($this->project);
        $other = User::factory()->createOne();
        $this->project->members()->attach($other->id, ['role' => ProjectRole::EXECUTOR->value]);

        $this->track($paid, $this->user, self::HOUR);
        $this->pay($paid, $this->user, self::HOUR);
        $this->track($partly, $this->user, self::HOUR);
        $this->pay($partly, $this->user, self::HOUR);
        $this->track($partly, $other, self::HOUR);

        $data = $this->list()->assertOk()->json('data');

        self::assertSame([$partly->id], array_column(array_column($data, 'task'), 'id'));
        self::assertSame([$other->id], array_column(array_column($data[0]['users'], 'user'), 'id'));
    }

    public function testIgnoresPaymentsOfCancelledRecords(): void
    {
        $task = $this->createTask($this->project);
        $this->track($task, $this->user, self::HOUR);
        $this->pay($task, $this->user, self::HOUR, FinanceStatus::CANCELLED);

        $data = $this->list()->assertOk()->json('data');

        self::assertSame(0, $data[0]['users'][0]['paid_seconds']);
    }

    public function testRunningTimerIsNotCounted(): void
    {
        $task = $this->createTask($this->project);

        TimeEntry::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'task_id' => $task->id,
            'description' => 'Таймер',
            'started_at' => Carbon::now()->subHour(),
            'stopped_at' => null,
        ]);

        $this->list()->assertOk()->assertJsonCount(0, 'data');
    }

    public function testExceptFinanceFreesItsOwnPaymentsAndKeepsItsSavedRate(): void
    {
        $member = User::factory()->createOne();
        $this->project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 35]);
        $task = $this->createTask($this->project);
        $this->track($task, $member, self::HOUR);
        $finance = $this->pay($task, $member, self::HOUR, rate: 20);

        $this->list()->assertOk()->assertJsonCount(0, 'data');

        $users = $this->list(['except_finance_id' => $finance->id])->assertOk()->json('data.0.users');

        self::assertSame(0, $users[0]['paid_seconds']);
        self::assertEquals(20, $users[0]['hourly_rate']);
    }

    public function testRejectsRecordOfAnotherProject(): void
    {
        $foreign = $this->createFinance($this->createProject($this->user));

        $this->list(['except_finance_id' => $foreign->id])->assertUnprocessable();
    }

    public function testFiltersByTitle(): void
    {
        $match = $this->createTask($this->project, 'Верстка лендінгу');
        $miss = $this->createTask($this->project, 'Дизайн');
        $this->track($match, $this->user, self::HOUR);
        $this->track($miss, $this->user, self::HOUR);

        $data = $this->list(['title' => 'верст'])->assertOk()->json('data');

        self::assertSame([$match->id], array_column(array_column($data, 'task'), 'id'));
    }

    public function testForbiddenForForeignProject(): void
    {
        $this->list([], $this->createProject(User::factory()->createOne()))->assertForbidden();
    }

    /**
     * @param array<string, mixed> $query
     */
    private function list(array $query = [], ?Project $project = null): TestResponse
    {
        return $this->getJson(action(ListFinanceTasksController::class, ['project' => ($project ?? $this->project)->id, ...$query]));
    }

    private function track(Task $task, User $user, int $seconds): void
    {
        $start = Carbon::parse('2026-11-01 10:00:00');

        TimeEntry::create([
            'user_id' => $user->id,
            'project_id' => $task->project_id,
            'task_id' => $task->id,
            'description' => $task->title,
            'started_at' => $start,
            'stopped_at' => $start->copy()->addSeconds($seconds),
        ]);
    }

    private function pay(Task $task, User $user, int $seconds, FinanceStatus $status = FinanceStatus::PLANNED, ?float $rate = null): Finance
    {
        $finance = $this->createFinance($this->project, $status);
        $finance->tasks()->attach($task->id, ['user_id' => $user->id, 'seconds' => $seconds, 'hourly_rate' => $rate]);

        return $finance;
    }

    private function createFinance(Project $project, FinanceStatus $status = FinanceStatus::PLANNED): Finance
    {
        return Finance::create([
            'user_id' => $this->user->id,
            'project_id' => $project->id,
            'title' => 'Оплата',
            'type' => 'expense',
            'currency' => 'usd',
            'amount' => 100,
            'exchange_rate' => 41.5,
            'amount_uah' => 4150,
            'status' => $status->value,
        ]);
    }

    private function createProject(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => 'Лендінг',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'description' => 'Тестовий проєкт',
            'budget' => 1000,
        ]);
    }

    private function createTask(Project $project, string $title = 'Верстка'): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => $title,
        ]);
    }
}
