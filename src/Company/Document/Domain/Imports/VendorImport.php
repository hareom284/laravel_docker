<?php

namespace Src\Company\Document\Domain\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Src\Company\Document\Domain\Model\Entities\VendorCategory;
use Src\Company\Document\Infrastructure\EloquentModels\VendorCategoryEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;

class VendorImport implements ToCollection, WithHeadingRow

{
    protected $sheetName;

    public function __construct($sheetName)
    {
        $this->sheetName = $sheetName;
    }
    public function collection(Collection $rows)
    {
        // removing excel header room for array
        $toArray = $rows->toArray();
        array_splice($toArray, 0, 1);
        $toCollection = collect($toArray);

        foreach ($toCollection as $row) 
        {

            $vendorCategory = null;

            if(isset($row[10]))
            {
                $vendorCategory = VendorCategoryEloquentModel::firstOrCreate([
                    'type' => $row[10],
                ]);
            }

            VendorEloquentModel::firstOrCreate([
                'vendor_name' => $row[0]
            ],[
                'contact_person' => $row[1],
                'contact_person_number' => $row[2],
                'email' => $row[3],
                'street_name' => $row[4],
                'block_num' => $row[5],
                'unit_num' => $row[6],
                'postal_code' => $row[7],
                'fax_number' => $row[8],
                'rebate' => $row[9],
                'vendor_category_id' => $vendorCategory ? $vendorCategory->id : $vendorCategory
            ]);

        }        
    }
}