<?php

namespace Src\Company\Document\Domain\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;

class VendorUpdateImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            VendorEloquentModel::where('vendor_name', $row[0])->update([
                'contact_person' => $row[1] ?? "N0 Name",
                'contact_person_number' => $row[2] ?? "09",
                'rebate' => $row[3],
            ]);
        }        
    }
}