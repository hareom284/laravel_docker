<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;


class AddToCartSeeder extends Seeder
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
                'name' => 'view_add_to_cart',
                "guard_name" => "api"
            ]
        ];

        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}