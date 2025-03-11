<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class TermAndConditionsPermissionSeeder extends Seeder
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
                'name' => 'access_term_and_conditions',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_term_and_conditions',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_term_and_conditions',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_term_and_conditions',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_term_and_conditions',
                "guard_name" => "api"
            ],
            
        ];
        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}
