<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertyTypes = ['HDB','Condominium','Landed','Commercial'];

        foreach ($propertyTypes as $propertyType) {
            PropertyTypeEloquentModel::create([
                'type' => $propertyType,
                'is_predefined' => true
            ]);
        }

    }
}
