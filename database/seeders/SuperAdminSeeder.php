<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            "id"  => 1,
            "first_name"    => "Super",
            "last_name" => "Admin",
            "email"     => "superadmin@mail.com",
            "password"       => bcrypt('password'),
            "contact_no"    => "09951613400",
            "is_active"         => 1,
            "email_verified_at" => Carbon::now(),
        ];


        $user = UserEloquentModel::create($data);
        $user->roles()->sync(['6']);
    }
}
