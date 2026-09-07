<?php

namespace App\Containers\AppSection\Project\UI\API\Transformers;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ProjectMemberTransformer extends ParentTransformer
{
    /**
     * @param User $item
     * @return array
     */
    public function transform(User $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'role' => $item->pivot->role,
        ];
    }
}
