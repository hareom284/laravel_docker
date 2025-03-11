<?php

namespace Src\Company\System\Domain\Imports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class StaffExcelImport implements ToCollection, WithHeadingRow

{

    public function collection(Collection $rows)
    {

        // removing excel header room for array
        $toArray = $rows->toArray();
        array_splice($toArray, 0, 1);
        $toCollection = collect($toArray);

        foreach ($toCollection as $value) {

            if(isset($value[1]) || isset($value[2])){

                $role = RoleEloquentModel::query()->firstOrCreate([
                    'name' => $value[6]
                ],[
                    'description' => null,
                ]);

                 // store to UserEloquentModel
                $newUser = UserEloquentModel::create([
                    'name_prefix' => $value[0],
                    'first_name' => $value[1],
                    'last_name' => $value[2],
                    'prefix' => $value[3],
                    'contact_no' => $value[4],
                    'email' => $value[5],
                    'is_active' => 1,
                    'password' => Hash::make('password')
                ]);

                // attach role
                $newUser->roles()->sync($role->id);

                // store to StaffEloquentModel
                if($role->id == 1){
                    $newStaff = StaffEloquentModel::create([
                        'user_id' => $newUser->id,
                        'rank_id' => 2,
                    ]);
                }
            }
        }
    }
}
