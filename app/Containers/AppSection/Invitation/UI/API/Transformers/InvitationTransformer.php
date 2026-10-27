<?php

namespace App\Containers\AppSection\Invitation\UI\API\Transformers;

use App\Containers\AppSection\Invitation\Models\Invitation;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class InvitationTransformer extends ParentTransformer
{
    public function transform(Invitation $invitation): array
    {
        return [
            'id' => $invitation->id,
            'email' => $invitation->email,
            'invited_by_name' => $invitation->inviter?->name,
            'expires_at' => $invitation->expires_at,
            'created_at' => $invitation->created_at,
        ];
    }
}
