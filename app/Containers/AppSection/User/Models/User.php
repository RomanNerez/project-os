<?php

namespace App\Containers\AppSection\User\Models;

use App\Containers\AppSection\Authentication\Notifications\ResetPassword;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\User\Data\Collections\UserCollection;
use App\Containers\AppSection\User\Enums\Gender;
use App\Ship\Parents\Models\UserModel as ParentUserModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class User extends ParentUserModel implements HasMedia
{
    use InteractsWithMedia;

    public const AVATAR_COLLECTION = 'avatar';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'birth',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'immutable_datetime',
        'password' => 'hashed',
        'gender' => Gender::class,
        'birth' => 'immutable_date',
    ];

    public function newCollection(array $models = []): UserCollection
    {
        return new UserCollection($models);
    }

    /**
     * Allows Passport to find the user by email (case-insensitive).
     */
    public function findForPassport(string $username): self|null
    {
        return self::orWhereRaw('lower(email) = lower(?)', [$username])->first();
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function isSuperAdmin(): bool
    {
        foreach (array_keys(config('auth.guards')) as $guard) {
            if (!$this->hasRole(RoleEnum::SUPER_ADMIN, $guard)) {
                return false;
            }
        }

        return true;
    }

    public function getAvatarUrl(): string|null
    {
        $url = $this->getFirstMediaUrl(self::AVATAR_COLLECTION, 'thumb');

        return '' === $url ? null : $url;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::AVATAR_COLLECTION)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->performOnCollections(self::AVATAR_COLLECTION)
            ->fit(Fit::Crop, 128, 128)
            ->nonQueued();
    }

    /**
     * Only first_name and last_name are stored; name is the display value built from them.
     */
    protected function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => trim($this->first_name . ' ' . $this->last_name),
        );
    }

    protected function email(): Attribute
    {
        return new Attribute(
            get: static fn (string|null $value): string|null => is_null($value) ? null : strtolower($value),
        );
    }
}
