<?php

namespace App\Containers\AppSection\Project\Providers;

use App\Containers\AppSection\Project\Models\ProjectUser;
use App\Containers\AppSection\Project\Policies\ProjectMemberPolicy;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;
use Illuminate\Support\Facades\Gate;

final class ProjectServiceProvider extends ParentServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Gate::policy(ProjectUser::class, ProjectMemberPolicy::class);
    }
}
