<?php

namespace Database\Seeders;

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class SeoProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoRole = Role::create([
            'name'       => 'Seo Head',
            'guard_name' => 'web',
        ]);

        $permissions = [
            [
                'name'       => 'seo-create',
                'guard_name' => 'web',
                'route'      => 'seo-create',
                'is_active'  => 1,
            ],
            [
                'name'       => 'seo-content',
                'guard_name' => 'web',
                'route'      => 'seo-content',
                'is_active'  => 1,
            ],
            [
                'name'       => 'seo-show',
                'guard_name' => 'web',
                'route'      => 'seo-show',
                'is_active'  => 1,
            ],
            [
                'name'       => 'seo-edit',
                'guard_name' => 'web',
                'route'      => 'seo-edit',
                'is_active'  => 1,
            ],
        ];

        Permission::insert($permissions);
    }
}
