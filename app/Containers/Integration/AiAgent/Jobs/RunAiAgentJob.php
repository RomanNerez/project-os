<?php

namespace App\Containers\Integration\AiAgent\Jobs;

use App\Containers\Integration\AiAgent\Actions\GenerateAiMessageAction;
use App\Containers\Integration\AiAgent\Enums\AiChatMessageStatus;
use App\Containers\Integration\AiAgent\Tasks\UpdateAiChatMessageTask;
use App\Ship\Parents\Jobs\Job as ParentJob;
use Throwable;

final class RunAiAgentJob extends ParentJob
{
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 1;

    public function __construct(
        private int $aiChatMessageId
    ) {}

    /**
     * @return void
     */
    public function handle(): void
    {
        app(GenerateAiMessageAction::class)->run($this->aiChatMessageId);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        app(UpdateAiChatMessageTask::class)->run($this->aiChatMessageId, [
            'status' => AiChatMessageStatus::FAILED,
        ]);
    }
}