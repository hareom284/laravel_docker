<?php

namespace Src\Company\Security\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Security\Domain\Model\User;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;

class UserMapper
{
    public static function fromRequest(Request $request, $user_id = null): User
    {
        return new User(

            id : $user_id,
            name : $request->name,
            email : $request->email,
            organization_id  : $request->organization_id,
            email_verified_at : $request->email_verified_at,
            dob : $request->dob,
            contact_number  : $request->contact_number,
            storage_limit : $request->storage_limit,
            password  : $request->password,
            is_active : $request->is_active,
            stripe_id : $request->stripe_id,
            pm_brand : $request->pm_brand,
            pm_last_four : $request->pm_last_four,
            trial_end_at : $request->trial_end_at,
        );
    }

    public static function toEloquent(User $user): UserEloquentModel
    {
        $UserEloquent = new UserEloquentModel();

        if ($user->id) {
            $UserEloquent = UserEloquentModel::query()->findOrFail($user->id);
        }
        $UserEloquent->name  =  $user->name;
        $UserEloquent->email  =  $user->email;
        $UserEloquent->organization_id   =  $user->organization_id;
        $UserEloquent->email_verified_at  =  $user->email_verified_at;
        $UserEloquent->dob  =  $user->dob;
        $UserEloquent->contact_number   =  $user->contact_number;
        $UserEloquent->storage_limit  =  $user->storage_limit;
        $UserEloquent->password   =  $user->password;
        $UserEloquent->is_active  =  $user->is_active;
        $UserEloquent->stripe_id  =  $user->stripe_id;
        $UserEloquent->pm_brand  =  $user->pm_brand;
        $UserEloquent->pm_last_four  =  $user->pm_last_four;
        $UserEloquent->trial_end_at  =  $user->trial_end_at;

        return $UserEloquent;
    }
}
