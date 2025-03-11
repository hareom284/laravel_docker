<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class ProjectListViewForMarketing extends Seeder
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
                'name' => 'access_marketing_project_lists',
                "guard_name" => "api"
            ],
        ];
        foreach ($permissions as $permission) {
            PermissionEloquentModel::create($permission);
        }
    }
}
