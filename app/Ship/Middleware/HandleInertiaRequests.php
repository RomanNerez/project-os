<?php

namespace App\Ship\Middleware;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Project\Models\Project;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $user = $request->user('web');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => null !== $user ? [
                    ...$user->only(['id', 'name', 'email']),
                    'avatar_url' => $user->getAvatarUrl(),
                    'unread_notifications_count' => $user->unreadNotifications()->count(),
                    'permissions' => [
                        'manage_users' => $user->can('index', [$user::class]),
                        'create_users' => $user->can('create', [$user::class]),
                        'create_projects' => $user->can('create', Project::class),
                        'manage_roles' => $user->can('create', Role::class),
                    ],
                ] : null,
            ],
        ];
    }
}
