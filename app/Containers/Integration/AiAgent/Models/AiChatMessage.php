<?php

namespace App\Containers\Integration\AiAgent\Models;

use App\Ship\Parents\Models\Model as ParentModel;
use LLPhant\Chat\Enums\ChatRole;

final class AiChatMessage extends ParentModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'role',
        'content',
        'tool_calls',
        'tokens_used',
        'model_name',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'role' => ChatRole::class,
        ];
    }
}
