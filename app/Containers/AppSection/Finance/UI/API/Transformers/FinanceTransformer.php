<?php

namespace App\Containers\AppSection\Finance\UI\API\Transformers;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class FinanceTransformer extends ParentTransformer
{
    /**
     * @var array<int, string>
     */
    protected array $defaultIncludes = [];

    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'tasks',
        'media',
    ];

    /**
     * Money is sent as strings so no precision is lost on the way to the client.
     *
     * @param Finance $item
     * @return array
     */
    public function transform(Finance $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'type' => $item->type,
            'currency' => $item->currency,
            'amount' => $item->amount,
            'exchange_rate' => $item->exchange_rate,
            'amount_uah' => $item->amount_uah,
            'status' => $item->status,
            'due_date' => $item->due_date?->toDateString(),
            'paid_at' => $item->paid_at?->toDateString(),
            'created_at' => $item->created_at?->toIso8601String(),
            'projects' => $item->tasks
                ->map(static fn (Task $task) => $task->project)
                ->filter()
                ->unique('id')
                ->map(static fn ($project) => ['id' => $project->id, 'title' => $project->title])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param Finance $finance
     * @return array
     */
    public function includeTasks(Finance $finance)
    {
        return $this->collection($finance->tasks, static fn (Task $task) => [
            'id' => $task->id,
            'title' => $task->title,
            'project_id' => $task->project_id,
        ]);
    }

    /**
     * @param Finance $finance
     * @return array
     */
    public function includeMedia(Finance $finance)
    {
        return $this->collection($finance->getMedia(Finance::RECEIPTS_COLLECTION), new MediaTransformer());
    }
}
