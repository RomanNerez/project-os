<?php

namespace App\Containers\AppSection\Media\Providers;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Policies\MediaPolicy;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;
use Illuminate\Support\Facades\Gate;

final class MediaServiceProvider extends ParentServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Gate::policy(Media::class, MediaPolicy::class);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        //
    }
}
