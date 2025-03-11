<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;


class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => 'Salesperson', "guard_name" => "web"],
            ['name' => 'Management', "guard_name" => "web"],
            ['name' => 'Drafter', "guard_name" => "web"],
            ['name' => 'Accountant', "guard_name" => "web"],
            ['name' => 'Customer', "guard_name" => "web"],
            ['name' => 'SuperAdmin', "guard_name" => "web"],
            ['name' => 'Marketing', "guard_name" => "web"],
            ['name' => 'Manager', "guard_name" => "web"],
        ];
        foreach ($datas as $data) {
            $role = RoleEloquentModel::firstOrCreate($data);
            //Permission access for according to config data on each permission
            $permission_ids = PermissionEloquentModel::whereIn('name', config("userrole.{$role->name}"))->pluck('id');

            foreach ($permission_ids as $permission_id) {
                if (!$role->permissions()->where('id', $permission_id)->exists()) {
                    logger('new permission added on these role', [PermissionEloquentModel::find($permission_id)->name, $role->name]);
                    $role->permissions()->attach($permission_id);
                }
            }
        }
    }
}
