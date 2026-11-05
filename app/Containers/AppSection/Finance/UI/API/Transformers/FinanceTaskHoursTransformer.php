<?php

namespace App\Containers\AppSection\Finance\UI\API\Transformers;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class FinanceTaskHoursTransformer extends ParentTransformer
{
    /**
     * @param array{
     *     task: array{id: int, title: string, project_id: int},
     *     users: list<array{user: User, tracked_seconds: int, paid_seconds: int, hourly_rate: null|string}>
     * } $item
     * @return array
     */
    public function transform(array $item): array
    {
        return [
            'task' => $item['task'],
            'users' => array_map(static fn (array $hours): array => [
                'user' => [
                    'id' => $hours['user']->id,
                    'name' => $hours['user']->name,
                    'avatar_url' => $hours['user']->getAvatarUrl(),
                ],
                'tracked_seconds' => $hours['tracked_seconds'],
                'paid_seconds' => $hours['paid_seconds'],
                'hourly_rate' => null === $hours['hourly_rate'] ? null : (float) $hours['hourly_rate'],
            ], $item['users']),
        ];
    }
}
