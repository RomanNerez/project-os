<?php

namespace App\Containers\AppSection\Finance\Models;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Finance extends ParentModel implements HasMedia
{
    use InteractsWithMedia;

    public const RECEIPTS_COLLECTION = 'receipts';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'currency',
        'amount',
        'exchange_rate',
        'amount_uah',
        'status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_uah' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tasks this payment is for; its projects are the projects of these tasks.
     *
     * @return BelongsToMany<Task, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'finance_task');
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::RECEIPTS_COLLECTION)
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * @param null|Media $media
     * @return void
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
