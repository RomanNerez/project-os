<?php

namespace App\Containers\AppSection\Task\UI\API\Transformers;

use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use Spatie\Activitylog\Models\Activity;

final class TaskActivityTransformer extends ParentTransformer
{
    /**
     * Logged attribute => field name the UI knows it by.
     *
     * @var array<string, string>
     */
    private const FIELDS = [
        'title' => 'title',
        'description' => 'description',
        'status' => 'status',
        'priority' => 'priority',
        'estimate' => 'estimate',
        'assignee.name' => 'assignee',
    ];

    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'causer',
    ];

    /**
     * @param Activity $item
     * @return array
     */
    public function transform(Activity $item): array
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $item->attribute_changes?->get('attributes', []) ?? [];
        /** @var array<string, mixed> $old */
        $old = $item->attribute_changes?->get('old', []) ?? [];

        // A created task has no "before"; the UI only says who created it.
        $changes = [];
        foreach ($item->event === 'created' ? [] : self::FIELDS as $attribute => $field) {
            if (!array_key_exists($attribute, $attributes)) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'old' => $old[$attribute] ?? null,
                'new' => $attributes[$attribute],
            ];
        }

        return [
            'id' => $item->id,
            'event' => $item->event,
            'created_at' => $item->created_at,
            'changes' => $changes,
        ];
    }

    /**
     * @param Activity $item
     * @return array
     */
    public function includeCauser(Activity $item)
    {
        return $this->nullableItem($item->causer, new UserTransformer());
    }
}
