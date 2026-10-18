<?php

namespace App\Containers\AppSection\Finance\Tests\Functional\WEB;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Finance\UI\WEB\Controllers\UpdateFinanceController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateFinanceController::class)]
final class UpdateFinanceTest extends WebTestCase
{
    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->project = $this->createProject($this->user);
        $this->actingAs($this->user, 'web');
    }

    public function testAuthorUpdatesFieldsAndAmountIsRecomputed(): void
    {
        $finance = $this->createFinance($this->user, $this->project);

        $this->update($finance, [
            'title' => 'Нова назва',
            'type' => 'income',
            'currency' => 'uah',
            'amount' => '2000',
            'exchange_rate' => '41',
            'status' => 'paid',
            'paid_at' => '2026-10-20',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $finance->refresh();

        self::assertSame('Нова назва', $finance->title);
        self::assertSame('income', $finance->type);
        self::assertSame('2000.00', $finance->amount_uah);
        self::assertNull($finance->exchange_rate);
        self::assertSame('2026-10-20', $finance->paid_at?->toDateString());
    }

    public function testMovesRecordToAnotherProjectWithItsTasks(): void
    {
        $finance = $this->createFinance($this->user, $this->project, [$this->createTask($this->project)->id]);
        $other = $this->createProject($this->user);
        $task = $this->createTask($other);

        $this->update($finance, ['project_id' => $other->id, 'task_ids' => [$task->id]])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $finance->refresh();

        self::assertSame($other->id, $finance->project_id);
        self::assertSame([$task->id], $finance->tasks->pluck('id')->all());
    }

    public function testRejectsTasksOfAnotherProject(): void
    {
        $finance = $this->createFinance($this->user, $this->project);
        $foreignTask = $this->createTask($this->createProject($this->user));

        $this->update($finance, ['task_ids' => [$foreignTask->id]])->assertSessionHasErrors('task_ids');

        self::assertSame(0, $finance->tasks()->count());
    }

    public function testProjectMemberWhoIsNotTheAuthorCannotUpdate(): void
    {
        $author = User::factory()->createOne();
        $project = $this->createProject($author);
        $project->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);
        $finance = $this->createFinance($author, $project);

        $this->update($finance, ['title' => 'Зламано'])->assertForbidden();

        self::assertSame('Оплата', $finance->refresh()->title);
    }

    public function testRemovesAndAddsReceipts(): void
    {
        $finance = $this->createFinance($this->user, $this->project);
        $finance->addMedia(UploadedFile::fake()->image('kept.png'))->toMediaCollection(Finance::RECEIPTS_COLLECTION);
        $removed = $finance->addMedia(UploadedFile::fake()->image('removed.png'))->toMediaCollection(Finance::RECEIPTS_COLLECTION);

        $foreign = $this->createFinance($this->user, $this->project);
        $foreignMedia = $foreign->addMedia(UploadedFile::fake()->image('foreign.png'))->toMediaCollection(Finance::RECEIPTS_COLLECTION);

        $this->update($finance, [
            'removed_receipt_ids' => [$removed->id, $foreignMedia->id],
            'receipts' => [UploadedFile::fake()->image('new.png')],
        ])->assertRedirect()->assertSessionHasNoErrors();

        self::assertEqualsCanonicalizing(
            ['kept.png', 'new.png'],
            $finance->refresh()->getMedia(Finance::RECEIPTS_COLLECTION)->pluck('file_name')->all(),
        );
        self::assertCount(1, $foreign->refresh()->getMedia(Finance::RECEIPTS_COLLECTION));
    }

    /**
     * Sent as a spoofed PUT, the way the multipart form does it.
     *
     * @param array<string, mixed> $overrides
     */
    private function update(Finance $finance, array $overrides): TestResponse
    {
        return $this->post(action(UpdateFinanceController::class, ['finance' => $finance->id]), [
            '_method' => 'put',
            'project_id' => $finance->project_id,
            'title' => $finance->title,
            'type' => $finance->type,
            'currency' => $finance->currency,
            'amount' => $finance->amount,
            'exchange_rate' => $finance->exchange_rate,
            'status' => $finance->status,
            ...$overrides,
        ]);
    }

    /**
     * @param array<int, int> $taskIds
     */
    private function createFinance(User $author, Project $project, array $taskIds = []): Finance
    {
        $finance = Finance::create([
            'user_id' => $author->id,
            'project_id' => $project->id,
            'title' => 'Оплата',
            'type' => 'expense',
            'currency' => 'usd',
            'amount' => 100,
            'exchange_rate' => 41.5,
            'amount_uah' => 4150,
            'status' => 'planned',
        ]);
        $finance->tasks()->sync($taskIds);

        return $finance;
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
