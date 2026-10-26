<?php

namespace App\Containers\AppSection\Invitation\Models;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $email
 * @property string $token_hash
 * @property int|null $invited_by
 * @property CarbonInterface $expires_at
 * @property CarbonInterface|null $accepted_at
 */
final class Invitation extends ParentModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token_hash',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Not yet accepted and not expired.
     */
    public function isUsable(): bool
    {
        return null === $this->accepted_at && $this->expires_at->isFuture();
    }
}
