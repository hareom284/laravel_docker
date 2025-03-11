<?php

namespace Src\Company\Document\Domain\Imports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionAreaOfWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplatesEloquentModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Src\Company\Document\Infrastructure\EloquentModels\MeasurementEloquentModel;

class TemplateImport implements ToCollection, WithHeadingRow

{
    protected $sheetName;

    public function __construct($sheetName)
    {
        $this->sheetName = $sheetName;
    }
    public function collection(Collection $rows)
    {
        $quotationTemplate = QuotationTemplatesEloquentModel::create([
            'name' => $this->sheetName,
            'saleperson_id' => null,
            'archive' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()

        ]);

        // removing excel header room for array
        $toArray = $rows->toArray();
        array_splice($toArray, 0, 1);
        $toCollection = collect($toArray);

        foreach ($toCollection as $row) 
        {
            
            if(isset($row[0]))
            {

                $section = SectionsEloquentModel::firstOrCreate([
                    'name' => trim($row[0]),
                    'quotation_template_id' => $quotationTemplate->id
                ],[
                    'saleperson_id' => null,
                    'index' => SectionsEloquentModel::where('salesperson_id', null)->count() + 1,
                    'calculation_type' => trim($row[8]),
                    'is_active' => 1,
                    'is_misc' => 0

                ]);
            }

            if(isset($row[1]))
            {

                $aow = SectionAreaOfWorkEloquentModel::firstOrCreate([
                    'section_id' => $section->id,
                    'name' => trim($row[1]),
                ],[
                    'index' => SectionAreaOfWorkEloquentModel::count() + 1,
                    'is_active' => 1
                ]);
            }

            if(isset($row[2]))
            {
                // Perform the calculation
                // $percentage = (($pWithGST - $costPrice) / $costPrice) * 100;

                $parentItemId = null;

                if($row[3] == 'SUB'){

                    if(!$parentItemId){
                        $lastItem = QuotationTemplateItemsEloquentModel::query()->whereNull('parent_id')->orderBy('id', 'desc')->first();

                        $parentItemId = $lastItem ? $lastItem->id : null;
                    }
                    
                } else {
                    $parentItemId = null;
                }

                if ((float)$row[7] > 0 && (float)$row[6] > 0) {
                    $percentage = (((float)$row[7] - (float)$row[6]) / (float)$row[6]) * 100;
                } else {
                    $percentage = 0;
                }

                // Format the result to two decimal places
                $profitMargin = number_format($percentage, 2);
                $profitMargin = str_replace(',', '', $profitMargin);

                $measurementEloquent = MeasurementEloquentModel::where('name',trim($row[5]))->first();

                QuotationTemplateItemsEloquentModel::create([
                    'description' => trim($row[2]),
                    'section_id' => $section->id,
                    'area_of_work_id' => $aow->id,
                    'parent_id' => $parentItemId,
                    'saleperson_id' => null,
                    'quantity' => (float)$row[4],
                    'unit_of_measurement' => trim($row[5]),
                    'price_with_gst' => round((float)$row[7], 2),
                    'cost_price' => round((float)$row[6], 2),
                    'profit_margin' => $profitMargin,
                    'is_fixed_measurement' => isset($measurementEloquent) ? $measurementEloquent->fixed : 0,
                    'sub_description' => isset($row[9]) ?  trim($row[9]) : null
                ]);

                // QuotationTemplateItemsEloquentModel::firstOrCreate([
                //     'description' => trim($row[2]),
                //     'section_id' => $section->id,
                //     'area_of_work_id' => $aow->id,
                // ],[
                //     'parent_id' => $parentItemId,
                //     'saleperson_id' => null,
                //     'quantity' => (float)$row[4],
                //     'unit_of_measurement' => trim($row[5]),
                //     'price_with_gst' => round((float)$row[7], 2),
                //     'cost_price' => round((float)$row[6], 2),
                //     'profit_margin' => $profitMargin
                // ]);
            }
        }
    }
}