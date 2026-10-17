<?php

namespace App\Containers\AppSection\Comment\Providers;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Policies\CommentPolicy;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;
use Illuminate\Support\Facades\Gate;

final class CommentServiceProvider extends ParentServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Gate::policy(Comment::class, CommentPolicy::class);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        //
    }
}
