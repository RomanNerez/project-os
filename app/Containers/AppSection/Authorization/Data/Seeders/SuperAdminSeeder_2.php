<?php

namespace App\Containers\AppSection\Authorization\Data\Seeders;

use App\Containers\AppSection\User\Actions\CreateAdminAction;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;

final class SuperAdminSeeder_2 extends ParentSeeder
{
    public function run(CreateAdminAction $action): void
    {
        $userData = [
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
        ];

        $action->run($userData);
    }
}
