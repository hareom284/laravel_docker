<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class ManagerProjectListViewPermissionSeeder extends Seeder
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
                'name' => 'view_team_project_lists',
                "guard_name" => "api"
            ],
            [
                'name' => 'view_team_project',
                "guard_name" => "api"
            ]
        ];
        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}
