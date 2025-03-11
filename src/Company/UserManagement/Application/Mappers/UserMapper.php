<?php

namespace Src\Company\UserManagement\Application\Mappers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Illuminate\Support\Facades\Storage;
use Src\Common\Domain\Model\ValueObjects\ContactNumber;

class UserMapper
{
    public static function fromRequest(Request $request, ?int $user_id = null): User
    {
        if ($request->hasFile('profile_pic')) {

            $picName =  time() . '.' . $request->file('profile_pic')->extension();

            $filePath = 'profile_pic/' . $picName;

            Storage::disk('public')->put($filePath, file_get_contents($request->file('profile_pic')));

            $profile_pic = $picName;
        } else {
            // Set logo to null if not provided
            $profile_pic = $request->original_pic ? $request->original_pic : null;
        }
        $contact_no = new ContactNumber(
            prefix: $request->prefix ?: null,
            contact_no: $request->contact_no ?: ''
        );
        return new User(
            id: $user_id,
            first_name: $request->first_name ? $request->first_name : null,
            last_name: $request->last_name ? $request->last_name : ' ',
            email: $request->email ? $request->email : null,
            contact_no: $contact_no,
            profile_pic: $profile_pic,
            name_prefix: $request->name_prefix ? $request->name_prefix : null,
            is_active: 1,
            quick_book_user_id: null,
            commission: $request->commission
        );
    }

    public static function fromEloquent(UserEloquentModel $userEloquent): User
    {
        $contact_no = new ContactNumber(
            prefix: $userEloquent->prefix,
            contact_no: $userEloquent->contact_no
        );
        return new User(
            id: $userEloquent->id,
            first_name: $userEloquent->first_name,
            last_name: $userEloquent->last_name,
            email: $userEloquent->email,
            contact_no: $contact_no,
            profile_pic: $userEloquent->profile_pic,
            name_prefix: $userEloquent->name_prefix,
            is_active: $userEloquent->is_active,
            quick_book_user_id: $userEloquent->quick_book_user_id,
            commission: $userEloquent->commission
        );
    }

    public static function toEloquent(User $user): UserEloquentModel
    {
        $userEloquent = new UserEloquentModel();
        if ($user->id) {
            $userEloquent = UserEloquentModel::query()->findOrFail($user->id);
        }
        $userEloquent->first_name = $user->first_name;
        $userEloquent->last_name = $user->last_name;
        $userEloquent->email = $user->email;
        $userEloquent->prefix = $user->contact_no->getPrefix();
        $userEloquent->contact_no = $user->contact_no->getContactNo();
        $userEloquent->profile_pic = $user->profile_pic ? $user->profile_pic : $userEloquent->profile_pic;
        $userEloquent->name_prefix = $user->name_prefix;
        $userEloquent->is_active = $user->is_active;
        $userEloquent->quick_book_user_id = $user->quick_book_user_id;
        $userEloquent->commission = $user->commission;
        return $userEloquent;
    }
}
