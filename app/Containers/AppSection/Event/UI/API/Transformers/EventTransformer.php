<?php

namespace App\Containers\AppSection\Event\UI\API\Transformers;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class EventTransformer extends ParentTransformer
{
    /**
     * @var array<int, string>
     */
    protected array $defaultIncludes = [];

    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'participants',
    ];

    /**
     * All-day events come as `YYYY-MM-DD` dates (end exclusive), timed ones as ISO 8601 instants,
     * which is what FullCalendar expects.
     */
    public function transform(Event $item): array
    {
        $user = auth()->user();
        $format = static fn ($date): string|null => $item->all_day ? $date?->toDateString() : $date?->toIso8601String();

        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'project_id' => $item->project_id,
            'project' => null === $item->project ? null : ['id' => $item->project->id, 'title' => $item->project->title],
            'title' => $item->title,
            'description' => $item->description,
            'location' => $item->location,
            'type' => $item->type,
            'color' => $item->color,
            'start_at' => $format($item->start_at),
            'end_at' => $format($item->end_at),
            'all_day' => $item->all_day,
            'reminder_minutes' => $item->reminder_minutes,
            'my_status' => null === $user ? null : $item->participantStatus($user)?->value,
            'permissions' => [
                'update' => $user?->can('update', $item) ?? false,
                'delete' => $user?->can('delete', $item) ?? false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function includeParticipants(Event $event)
    {
        return $this->collection($event->participants, static fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'avatar_url' => $user->getAvatarUrl(),
            'status' => $user->pivot->status,
        ]);
    }
}
