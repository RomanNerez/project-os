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
        $task = $this->createTask($this->createProject($this->user));

        $this->store(['task_ids' => [$task->id], 'exchange_rate' => 41.5])->assertRedirect()->assertSessionHasNoErrors();

        $finance = Finance::sole();

        self::assertSame('1500.00', $finance->amount);
        self::assertSame('1500.00', $finance->amount_uah);
        self::assertNull($finance->exchange_rate);
        self::assertSame($this->user->id, $finance->user_id);
        self::assertSame([$task->id], $finance->tasks->pluck('id')->all());
    }

    public function testCreatesUsdRecordForTasksFromDifferentProjects(): void
    {
        $own = $this->createTask($this->createProject($this->user));
        $foreignProject = $this->createProject(User::factory()->createOne());
        $foreignProject->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);
        $shared = $this->createTask($foreignProject);

        $this->store([
            'currency' => 'usd',
            'amount' => '100.50',
            'exchange_rate' => '41.2345',
            'task_ids' => [$own->id, $shared->id],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $finance = Finance::sole();

        self::assertSame('41.2345', $finance->exchange_rate);
        self::assertSame('4144.07', $finance->amount_uah);
        self::assertEqualsCanonicalizing([$own->id, $shared->id], $finance->tasks->pluck('id')->all());
    }

    public function testUsdRequiresExchangeRate(): void
    {
        $task = $this->createTask($this->createProject($this->user));

        $this->store(['currency' => 'usd', 'task_ids' => [$task->id]])->assertSessionHasErrors('exchange_rate');

        self::assertSame(0, Finance::count());
    }

    public function testRequiresAtLeastOneTask(): void
    {
        $this->store(['task_ids' => []])->assertSessionHasErrors('task_ids');

        self::assertSame(0, Finance::count());
    }

    public function testRejectsTaskFromProjectTheUserDoesNotParticipateIn(): void
    {
        $own = $this->createTask($this->createProject($this->user));
        $foreign = $this->createTask($this->createProject(User::factory()->createOne()));

        $this->store(['task_ids' => [$own->id, $foreign->id]])->assertSessionHasErrors('task_ids');

        self::assertSame(0, Finance::count());
    }

    public function testStoresReceiptsInReceiptsCollection(): void
    {
        $task = $this->createTask($this->createProject($this->user));

        $this->store([
            'task_ids' => [$task->id],
            'receipts' => [
                UploadedFile::fake()->createWithContent('invoice.pdf', "%PDF-1.4\n%%EOF\n"),
                UploadedFile::fake()->image('check.png'),
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        self::assertCount(2, Finance::sole()->getMedia(Finance::RECEIPTS_COLLECTION));
    }

    public function testRejectsReceiptsOfOtherTypes(): void
    {
        $task = $this->createTask($this->createProject($this->user));

        $this->store([
            'task_ids' => [$task->id],
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
