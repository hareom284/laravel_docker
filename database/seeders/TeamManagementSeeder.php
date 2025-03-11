<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class TeamManagementSeeder extends Seeder
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
                'name' => 'access_team_management',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_team',
                "guard_name" => "api"
            ],
            [
                'name' => 'create_team',
                "guard_name" => "api"
            ],
            [
                'name' => 'update_team',
                "guard_name" => "api"
            ],
            [
                'name' => 'delete_team',
                "guard_name" => "api"
            ],
        ];
        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}
