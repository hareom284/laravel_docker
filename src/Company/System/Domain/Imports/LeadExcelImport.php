<?php

namespace Src\Company\System\Domain\Imports;

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
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListTemplateItemEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class LeadExcelImport implements ToCollection, WithHeadingRow

{
    public function collection(Collection $rows)
    {

        // removing excel header room for array
        $toArray = $rows->toArray();
        array_splice($toArray, 0, 1);
        $toCollection = collect($toArray);

        foreach ($toCollection as $value) {

            if(isset($value[1]) || isset($value[2])){
                 // store to UserEloquentModel
                $newUser = UserEloquentModel::create([
                    'name_prefix' => $value[0],
                    'first_name' => $value[1],
                    'last_name' => $value[2],
                    'prefix' => $value[3],
                    'contact_no' => $value[4],
                    'is_active' => 1,
                ]);

                // attach role
                $newUser->roles()->sync(5);

                // store to CustomerEloquentModel
                $newCustomer = CustomerEloquentModel::create([
                    'status' => 1,
                    'user_id' => $newUser->id
                ]);

                $checkListItemArray = [];

                $checkListItemEloquent = CheckListTemplateItemEloquentModel::all();

                foreach ($checkListItemEloquent as $checkListItem) {
                    array_push($checkListItemArray, $checkListItem->id);
                }

                // attach checklist template to customer
                $newCustomer->leadCheckLists()->attach($checkListItemArray);
            }

        }
    }
}
