<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteSettingEloquentModel::insert([
            'site_name' => 'DesignDen',
            'timezone' => 'UTC',
            'locale' => 'locale',
            'email' => 'hareom284@gmail.com',
            'contact_number' => '+959951613400',
            'ssl' => '-',
            'url' => '-',
        ]);
    }
}
