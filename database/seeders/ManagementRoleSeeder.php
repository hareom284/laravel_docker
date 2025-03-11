<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;


class ManagementRoleSeeder extends Seeder
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
                'name' => 'create_customer',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_customer',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_customer',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_customer',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_quotation',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_quotation',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_quotation',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_quotation',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_product',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_product',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_product',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_product',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_category',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_category',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_category',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_category',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_management_setting',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_management_setting',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_management_setting',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_management_setting',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_tagging',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_tagging',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_tagging',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_tagging',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_attribute',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_attribute',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_attribute',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_attribute',
                "guard_name" => "api"
            ],
        ];

        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}
