<?php

namespace Database\Seeders;

use App\Permission;
use Illuminate\Database\Seeder;

class DeployerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(
            [
                'name'       => 'deployer',
                'guard_name' => 'web',
                'route'      => 'deploy-node-list',
            ]
        );
    }
}
