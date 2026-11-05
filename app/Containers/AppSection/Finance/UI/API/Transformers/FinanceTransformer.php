<?php

namespace App\Containers\AppSection\Finance\UI\API\Transformers;

use Apiato\Http\Resources\Item;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use League\Fractal\Resource\Collection;

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
        'project',
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
        $user = auth()->user();

        return [
            'id' => $item->id,
            'project_id' => $item->project_id,
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
            'permissions' => [
                'update' => $user?->can('update', $item) ?? false,
            ],
        ];
    }

    /**
     * Every paid task once, with what this record pays for it per user.
     *
     * @param Finance $finance
     * @return Collection
     */
    public function includeTasks(Finance $finance): Collection
    {
        $paymentsByTask = $finance->taskPayments->groupBy('id');
        $taskTransformer = new TaskTransformer();

        return $this->collection(
            $finance->taskPayments->unique('id')->values(),
            static fn (Task $task): array => [
                ...$taskTransformer->transform($task),
                'payments' => $paymentsByTask->get($task->id, collect())
                    ->map(static fn (Task $row): array => [
                        'user_id' => (int) $row->pivot->user_id,
                        'seconds' => (int) $row->pivot->seconds,
                        'hourly_rate' => $row->pivot->hourly_rate,
                    ])
                    ->values()
                    ->all(),
            ],
        );
    }

    /**
     * @param Finance $finance
     * @return Collection
     */
    public function includeMedia(Finance $finance): Collection
    {
        return $this->collection($finance->getMedia(Finance::RECEIPTS_COLLECTION), new MediaTransformer());
    }

    /**
     * @param Finance $finance
     * @return Item
     */
    public function includeProject(Finance $finance): Item
    {
        return $this->item($finance->project, new ProjectTransformer());
    }
}
