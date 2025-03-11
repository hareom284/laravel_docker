<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;

class CheckListTemplateItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data1 = [
            "checklist_item_name"    => "Contacted",
            "checklist_item_value" => null,
            "checklist_item_desc"     => null
        ];

        $item1 = CheckListTemplateItemEloquentModel::create($data1);

        $data2 = [
            "checklist_item_name"    => "QO Sent",
            "checklist_item_value" => null,
            "checklist_item_desc"     => null
        ];

        $item2 = CheckListTemplateItemEloquentModel::create($data2);
    }
}
