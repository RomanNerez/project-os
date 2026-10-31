<?php

namespace App\Containers\AppSection\Authorization\Data\Seeders;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Models\Permission as PermissionModel;
use App\Containers\AppSection\Authorization\Tasks\CreatePermissionTask;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;
use Spatie\Permission\PermissionRegistrar;

final class AuthorizationPermissionsSeeder_3 extends ParentSeeder
{
    public function run(CreatePermissionTask $task): void
    {
        foreach (Permission::cases() as $permission) {
            $exists = PermissionModel::where('name', $permission->value)
                ->where('guard_name', Permission::GUARD)
                ->exists();

            if (!$exists) {
                $task->run($permission->value, null, $permission->label(), Permission::GUARD);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
