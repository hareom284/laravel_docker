<?php

namespace Database\Seeders;

use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class SalepersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $company_data = [
            "name" => "Testing Company"
        ];
        $company = CompanyEloquentModel::create($company_data);

        $rank_data = [
            "rank_name" => "Testing Rank"
        ];
        $rank = RankEloquentModel::create($rank_data);

        $data1 = [
            "first_name"    => "Saleperson",
            "last_name"    => "One",
            "email"     => "saleperson1@mail.com",
            "password"       => bcrypt('password'),
            "contact_no"    => "09951613400",
            "is_active"         => 1,
            "email_verified_at" => Carbon::now(),
        ];

        $user1 = UserEloquentModel::create($data1);
        $user1->roles()->sync(['1']);

        $staff_data1 = [
            "user_id" => $user1->id,
            "rank_id" => $rank->id
        ];

        StaffEloquentModel::create($staff_data1);

        // User 2
        $data2 = [
            "first_name"    => "Saleperson",
            "last_name"    => "Two",
            "email"     => "saleperson2@mail.com",
            "password"       => bcrypt('password'),
            "contact_no"    => "09951613401",
            "is_active"         => 1,
            "email_verified_at" => Carbon::now(),
        ];

        $user2 = UserEloquentModel::create($data2);
        $user2->roles()->sync(['1']);

        $staff_data2 = [
            "user_id" => $user2->id,
            "rank_id" => $rank->id
        ];

        StaffEloquentModel::create($staff_data2);

        // User 3
        $data3 = [
            "first_name"    => "Saleperson",
            "last_name"    => "Three",
            "email"     => "saleperson3@mail.com",
            "password"       => bcrypt('password'),
            "contact_no"    => "09951613402",
            "is_active"         => 1,
            "email_verified_at" => Carbon::now(),
        ];

        $user3 = UserEloquentModel::create($data3);
        $user3->roles()->sync(['1']);

        $staff_data3 = [
            "user_id" => $user3->id,
            "rank_id" => $rank->id
        ];

        StaffEloquentModel::create($staff_data3);

    }
}
