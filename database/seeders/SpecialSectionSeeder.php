<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;

class SpecialSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $special_section = SectionsEloquentModel::create([
            'name' => 'Miscellaneous',
            'index' => 0,
            'calculation_type' => 'LUMP_SUM',
            'is_active' => true,
            'is_misc' => true
        ]);

        QuotationTemplateItemsEloquentModel::create([
            'description' => 'testing',
            'quantity' => 1,
            'unit_of_measurement' => 'Total Amount',
            'section_id' => $special_section->id,
            'price_with_gst' => 0,
            'price_without_gst' => 0,
            'cost_price' => 0,
            'profit_margin' => 0,
        ]);
    }
}