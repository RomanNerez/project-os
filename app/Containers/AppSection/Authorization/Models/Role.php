<?php

namespace App\Containers\AppSection\Authorization\Models;

use Apiato\Core\Models\InteractsWithApiato;
use Apiato\Http\Resources\ResourceKeyAware;
use App\Containers\AppSection\Authorization\Data\Collections\RoleCollection;
use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole implements ResourceKeyAware
{
    use InteractsWithApiato;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    public function newCollection(array $models = []): RoleCollection
    {
        return new RoleCollection($models);
    }

    /**
     * Workspace roles are managed from the settings page; the super admin role is a system one.
     */
    public function isWorkspaceRole(): bool
    {
        return Permission::GUARD === $this->guard_name
            && RoleEnum::SUPER_ADMIN->value !== $this->name;
    }

    public static function workspaceRoleExistsRule(): Exists
    {
        return Rule::exists('roles', 'id')
            ->where('guard_name', Permission::GUARD)
            ->whereNot('name', RoleEnum::SUPER_ADMIN->value);
    }
}
