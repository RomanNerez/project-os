<?php

namespace App\Containers\AppSection\Project\UI\API\Transformers;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ProjectMemberTransformer extends ParentTransformer
{
    /**
     * @param bool $showHourlyRate hourly rate is visible only to those who manage members
     */
    public function __construct(
        private readonly bool $showHourlyRate = false,
    ) {}

    /**
     * @param User $item
     * @return array
     */
    public function transform(User $item): array
    {
        $data = [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'avatar_url' => $item->getAvatarUrl(),
            'role' => $item->pivot->role,
        ];

        if ($this->showHourlyRate) {
            $data['hourly_rate'] = null === $item->pivot->hourly_rate ? null : (float) $item->pivot->hourly_rate;
        }

        return $data;
    }
}
