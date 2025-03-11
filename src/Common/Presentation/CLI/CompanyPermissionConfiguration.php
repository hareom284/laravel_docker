<?php

namespace Src\Common\Presentation\CLI;

use Database\Seeders\PermissionRoleTableSeeder;
use Database\Seeders\PermissionTableSeeder;
use Src\Common\Domain\CommandInterface;
use Illuminate\Console\Command;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;

class CompanyPermissionConfiguration extends Command
{
    protected $signature = 'permissions:setup';


    protected $description = 'setup permissions for specific company';


    public function handle()
    {

        // Used for default company permissions setup
        $this->call(PermissionTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);

        $companyName = env('COMPANY_FOLDER_NAME');
        if(empty($companyName)) return;
        $companiesRolePermissions = config("companypermissions.$companyName");

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
            $permission_ids = PermissionEloquentModel::whereIn('name', $companiesRolePermissions[$data['name']])->pluck('id');

            foreach ($permission_ids as $permission_id) {
                if (!$role->permissions()->where('id', $permission_id)->exists()) {
                    logger('new permission added on these role', [PermissionEloquentModel::find($permission_id)->name, $role->name]);
                    $role->permissions()->attach($permission_id);
                }
            }
        }


        info('done setup permissions for specific company');

        return 0;
    }
}
