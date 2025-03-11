<?php

namespace Src\Company\UserManagement\Domain\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class UserUpdateImport implements ToCollection

{
    public function collection(Collection $rows)
    {
        foreach($rows as $row){
            
            
            $user = UserEloquentModel::where('first_name', $row[0])->first();

            if($user){

                $user->contact_no = $row[2] ?? "09";

                $user->save();

                $customer = CustomerEloquentModel::where('user_id', $user->id)->first();

                if ($customer) {
                    $customer->update([
                        'nric' => $row[1] ?? null,
                    ]);

                    $property = PropertyEloquentModel::create([
                        'type_id' => 1,
                        'street_name' => $row[3],
                        'block_num' => $row[4],
                        'unit_num' => substr($row[5], 1),
                        'postal_code' => $row[6],
                    ]);

                    // Attach the property to the customer
                    $customer->customer_properties()->attach($property->id);
                }
                
            }
        }
    }
}