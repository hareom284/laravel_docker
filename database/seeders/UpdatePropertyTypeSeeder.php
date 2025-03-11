<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;

class UpdatePropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertyTypes = ['Walk-Up Apartment', 'F&B', 'Shophouse', 'Office'];

        foreach ($propertyTypes as $propertyType) {
            PropertyTypeEloquentModel::create([
                'type' => $propertyType,
                'is_predefined' => true
            ]);
        }

    }
}
