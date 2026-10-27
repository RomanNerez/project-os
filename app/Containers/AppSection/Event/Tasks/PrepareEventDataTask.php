<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final class PrepareEventDataTask extends ParentTask
{
    public function __construct(
        private readonly ListParticipantCandidateIdsTask $listParticipantCandidateIdsTask,
    ) {
    }

    /**
     * The project must be visible to the user and every participant must be a candidate
     * (see ListParticipantCandidateIdsTask); the author is never a participant of their own event.
     *
     * @param array<string, mixed> $data validated create/update payload
     * @param int $userId the acting user
     * @param int $authorId the event's author (the acting user on create)
     *
     * @return array{0: array<string, mixed>, 1: array<int, int>} event attributes and participant ids
     *
     * @throws ValidationException
     */
    public function run(array $data, int $userId, int $authorId): array
    {
        $projectId = isset($data['project_id']) ? (int) $data['project_id'] : null;

        if (null !== $projectId && !Project::query()->visibleTo($userId)->whereKey($projectId)->exists()) {
            throw ValidationException::withMessages(['project_id' => 'Проєкт недоступний.']);
        }

        $participantIds = collect($data['participant_ids'] ?? [])
            ->map(static fn (mixed $id): int => (int) $id)
            ->reject(static fn (int $id): bool => $id === $authorId)
            ->unique()
            ->values();

        if ($participantIds->diff($this->listParticipantCandidateIdsTask->run($authorId, $projectId))->isNotEmpty()) {
            throw ValidationException::withMessages(['participant_ids' => null === $projectId ? 'Запросити можна лише колег зі спільних проєктів.' : 'Учасники мають бути в складі вибраного проєкту.']);
        }

        return [[
            'project_id' => $projectId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'type' => $data['type'],
            'color' => $data['color'] ?? null,
            'reminder_minutes' => $data['reminder_minutes'] ?? null,
            ...self::period($data),
        ], $participantIds->all()];
    }

    /**
     * All-day events are stored as UTC midnights of their dates, timed ones as the exact instant.
     *
     * @param array<string, mixed> $data
     *
     * @return array{start_at: CarbonImmutable, end_at: CarbonImmutable|null, all_day: bool}
     */
    public static function period(array $data): array
    {
        $allDay = (bool) ($data['all_day'] ?? false);
        $timezone = config()->string('app.timezone');

        $parse = static fn (mixed $value): CarbonImmutable|null => null === $value || '' === $value
            ? null
            : ($allDay
                ? CarbonImmutable::parse(substr((string) $value, 0, 10), $timezone)->startOfDay()
                : CarbonImmutable::parse((string) $value)->setTimezone($timezone));

        return [
            'start_at' => $parse($data['start_at']) ?? throw new \InvalidArgumentException('start_at is required'),
            'end_at' => $parse($data['end_at'] ?? null),
            'all_day' => $allDay,
        ];
    }
}
