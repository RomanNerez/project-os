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

        $this->store(['project_id' => $project->id, 'task_ids' => [$task->id], 'exchange_rate' => 41.5])
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
        $project->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);
        $tasks = [$this->createTask($project)->id, $this->createTask($project)->id];

        $this->store([
            'project_id' => $project->id,
            'currency' => 'usd',
            'amount' => '100.50',
            'exchange_rate' => '41.2345',
            'task_ids' => $tasks,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $finance = Finance::sole();

        self::assertSame('41.2345', $finance->exchange_rate);
        self::assertSame('4144.07', $finance->amount_uah);
        self::assertEqualsCanonicalizing($tasks, $finance->tasks->pluck('id')->all());
    }

    public function testCreatesRecordWithoutTasks(): void
    {
        $project = $this->createProject($this->user);

        $this->store(['project_id' => $project->id])->assertRedirect()->assertSessionHasNoErrors();

        self::assertSame(0, Finance::sole()->tasks()->count());
    }

    public function testUsdRequiresExchangeRate(): void
    {
        $project = $this->createProject($this->user);

        $this->store(['project_id' => $project->id, 'currency' => 'usd'])->assertSessionHasErrors('exchange_rate');

        self::assertSame(0, Finance::count());
    }

    public function testRequiresProject(): void
    {
        $this->store([])->assertSessionHasErrors('project_id');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsProjectTheUserDoesNotParticipateIn(): void
    {
        $foreign = $this->createProject(User::factory()->createOne());

        $this->store(['project_id' => $foreign->id])->assertSessionHasErrors('project_id');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsTaskFromAnotherProject(): void
    {
        $project = $this->createProject($this->user);
        $other = $this->createTask($this->createProject($this->user));

        $this->store(['project_id' => $project->id, 'task_ids' => [$other->id]])->assertSessionHasErrors('task_ids');

        self::assertSame(0, Finance::count());
    }

    public function testStoresReceiptsInReceiptsCollection(): void
    {
        $this->store([
            'project_id' => $this->createProject($this->user)->id,
            'receipts' => [
                UploadedFile::fake()->createWithContent('invoice.pdf', "%PDF-1.4\n%%EOF\n"),
                UploadedFile::fake()->image('check.png'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        self::assertCount(2, Finance::sole()->getMedia(Finance::RECEIPTS_COLLECTION));
    }

    public function testRejectsReceiptsOfOtherTypes(): void
    {
        $this->store([
            'project_id' => $this->createProject($this->user)->id,
            'receipts' => [UploadedFile::fake()->create('setup.exe', 10, 'application/octet-stream')],
        ])->assertSessionHasErrors('receipts.0');

        self::assertSame(0, Finance::count());
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function store(array $overrides): TestResponse
    {
        return $this->post(action(CreateFinanceController::class), [
            'title' => 'Оплата верстки',
            'type' => 'expense',
            'currency' => 'uah',
            'amount' => '1500',
            'status' => 'planned',
            'due_date' => '2026-10-31',
            ...$overrides,
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

    private function createTask(Project $project): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => 'Верстка',
        ]);
    }
}
