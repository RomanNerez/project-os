<?php

namespace App\Containers\AppSection\Task\Providers;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Policies\TaskPolicy;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;
use Illuminate\Support\Facades\Gate;

final class TaskServiceProvider extends ParentServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        //
    }
}
