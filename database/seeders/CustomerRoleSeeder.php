<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;


class CustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'create_ecatalog',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_ecatalog',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_ecatalog',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_ecatalog',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_help_center',
                "guard_name" => "api"
            ],
        ];

        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}