<?php

namespace App\Containers\AppSection\Finance\Tests\Functional\WEB;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Finance\UI\WEB\Controllers\CreateFinanceController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateFinanceController::class)]
final class CreateFinanceTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testCreatesUahRecordForOneTask(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $this->user->id, 'seconds' => 3600]],
            'exchange_rate' => 41.5,
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $finance = Finance::sole();

        self::assertSame('1500.00', $finance->amount);
        self::assertSame('1500.00', $finance->amount_uah);
        self::assertNull($finance->exchange_rate);
        self::assertSame($this->user->id, $finance->user_id);
        self::assertSame($project->id, $finance->project_id);
        self::assertSame([$task->id], $finance->tasks->pluck('id')->all());
    }

    public function testCreatesUsdRecordInSharedProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => ProjectRole::EXECUTOR->value]);
        $tasks = [$this->createTask($project)->id, $this->createTask($project)->id];

        $this->store($project, [
            'currency' => 'usd',
            'amount' => '100.50',
            'exchange_rate' => '41.2345',
            'task_ids' => $tasks,
            'task_payments' => array_map(fn (int $id): array => ['task_id' => $id, 'user_id' => $this->user->id], $tasks),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $finance = Finance::sole();

        self::assertSame('41.2345', $finance->exchange_rate);
        self::assertSame('4144.07', $finance->amount_uah);
        self::assertEqualsCanonicalizing($tasks, $finance->tasks->pluck('id')->all());
    }

    public function testCreatesRecordWithoutTasks(): void
    {
        $project = $this->createProject($this->user);

        $this->store($project)->assertRedirect()->assertSessionHasNoErrors();

        self::assertSame(0, Finance::sole()->tasks()->count());
    }

    public function testUsdRequiresExchangeRate(): void
    {
        $project = $this->createProject($this->user);

        $this->store($project, ['currency' => 'usd'])->assertSessionHasErrors('exchange_rate');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsProjectTheUserDoesNotParticipateIn(): void
    {
        $foreign = $this->createProject(User::factory()->createOne());

        $this->store($foreign)->assertForbidden();

        self::assertSame(0, Finance::count());
    }

    public function testRejectsTaskFromAnotherProject(): void
    {
        $project = $this->createProject($this->user);
        $other = $this->createTask($this->createProject($this->user));

        $this->store($project, [
            'task_ids' => [$other->id],
            'task_payments' => [['task_id' => $other->id, 'user_id' => $this->user->id]],
        ])->assertSessionHasErrors('task_ids');

        self::assertSame(0, Finance::count());
    }

    public function testStoresPaymentsWithRateCopiedFromProjectMember(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 20]);
        $task = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [
                ['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 7200],
                ['task_id' => $task->id, 'user_id' => $this->user->id],
            ],
            'amount' => '1660',
            'exchange_rate' => 41.5,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $rows = Finance::sole()->taskPayments->keyBy('pivot.user_id');

        self::assertSame(7200, (int) $rows[$member->id]->pivot->seconds);
        self::assertSame('20.00', $rows[$member->id]->pivot->hourly_rate);
        // The owner has no project_user row, so no rate; seconds default to 0.
        self::assertSame(0, (int) $rows[$this->user->id]->pivot->seconds);
        self::assertNull($rows[$this->user->id]->pivot->hourly_rate);
    }

    public function testSavedRateDoesNotFollowLaterRateChanges(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 20]);
        $task = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 60]],
            'exchange_rate' => 41.5,
        ])->assertSessionHasNoErrors();

        $project->members()->updateExistingPivot($member->id, ['hourly_rate' => 35]);

        self::assertSame('20.00', Finance::sole()->taskPayments->sole()->pivot->hourly_rate);
    }

    public function testRejectsTaskWithoutPayments(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->store($project, ['task_ids' => [$task->id]])->assertSessionHasErrors('task_payments');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsPaymentForUnselectedTask(): void
    {
        $project = $this->createProject($this->user);
        $selected = $this->createTask($project);
        $unselected = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$selected->id],
            'task_payments' => [
                ['task_id' => $selected->id, 'user_id' => $this->user->id],
                ['task_id' => $unselected->id, 'user_id' => $this->user->id],
            ],
        ])->assertSessionHasErrors('task_payments.1.task_id');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsPaymentToUserOutsideProject(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => User::factory()->createOne()->id]],
        ])->assertSessionHasErrors('task_payments');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsNegativeSeconds(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $this->user->id, 'seconds' => -1]],
        ])->assertSessionHasErrors('task_payments.0.seconds');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsAmountBelowPaidHoursInUsd(): void
    {
        [$project, $task, $member] = $this->projectWithRatedMember(20);

        $this->store($project, [
            'currency' => 'usd',
            'amount' => '29.99',
            'exchange_rate' => 41.5,
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 5400]],
        ])->assertSessionHasErrors('amount');

        self::assertSame(0, Finance::count());
    }

    public function testAcceptsAmountEqualToPaidHoursInUsd(): void
    {
        [$project, $task, $member] = $this->projectWithRatedMember(20);

        $this->store($project, [
            'currency' => 'usd',
            'amount' => '30',
            'exchange_rate' => 41.5,
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 5400]],
        ])->assertRedirect()->assertSessionHasNoErrors();

        self::assertSame(1, Finance::count());
    }

    public function testUahMinimumUsesExchangeRate(): void
    {
        [$project, $task, $member] = $this->projectWithRatedMember(20);
        $payload = [
            'currency' => 'uah',
            'exchange_rate' => 40,
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 3600]],
        ];

        $this->store($project, [...$payload, 'amount' => '799.99'])->assertSessionHasErrors('amount');
        $this->store($project, [...$payload, 'amount' => '800'])->assertSessionHasNoErrors();

        self::assertSame(1, Finance::count());
    }

    public function testUahWithPaidHoursRequiresExchangeRate(): void
    {
        [$project, $task, $member] = $this->projectWithRatedMember(20);

        $this->store($project, [
            'task_ids' => [$task->id],
            'task_payments' => [['task_id' => $task->id, 'user_id' => $member->id, 'seconds' => 3600]],
        ])->assertSessionHasErrors('exchange_rate');

        self::assertSame(0, Finance::count());
    }

    public function testStoresReceiptsInReceiptsCollection(): void
    {
        $this->store($this->createProject($this->user), [
            'receipts' => [
                UploadedFile::fake()->createWithContent('invoice.pdf', "%PDF-1.4\n%%EOF\n"),
                UploadedFile::fake()->image('check.png'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        self::assertCount(2, Finance::sole()->getMedia(Finance::RECEIPTS_COLLECTION));
    }

    public function testRejectsReceiptsOfOtherTypes(): void
    {
        $this->store($this->createProject($this->user), [
            'receipts' => [UploadedFile::fake()->create('setup.exe', 10, 'application/octet-stream')],
        ])->assertSessionHasErrors('receipts.0');

        self::assertSame(0, Finance::count());
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function store(Project $project, array $overrides = []): TestResponse
    {
        return $this->post(action(CreateFinanceController::class, ['project' => $project->id]), [
            'title' => 'Оплата верстки',
            'type' => 'expense',
            'currency' => 'uah',
            'amount' => '1500',
            'status' => 'planned',
            'due_date' => '2026-10-31',
            ...$overrides,
        ]);
    }

    /**
     * @return array{0: Project, 1: Task, 2: User}
     */
    private function projectWithRatedMember(float $rate): array
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => $rate]);

        return [$project, $this->createTask($project), $member];
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

    private function createTask(Project $project): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => 'Верстка',
        ]);
    }
}
