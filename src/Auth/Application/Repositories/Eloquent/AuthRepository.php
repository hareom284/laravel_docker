<?php

namespace Src\Auth\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Src\Auth\Domain\Resources\AuthResource;
use Src\Auth\Domain\Resources\PermissionsResource;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;


class AuthRepository implements AuthRepositoryInterface
{
    //login
    public function login($credentials)
    {

        $user = UserEloquentModel::query()->where('email', $credentials['email'])->where('is_active', '=', 1)->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {

            return false;
        }

        $data['token'] = $user->createToken("API Token")->plainTextToken;

        $data['user'] = new AuthResource($user);

        return $data;
    }

    //verification email
    public function verification($id)
    {


        $decode_id = Crypt::decryptString($id);
        $user = UserEloquentModel::findOrFail($decode_id);
        $user->update([
            "email_verified_at" => Carbon::now()
        ]);

        return  $user;
    }

    public function findUserByEmail($email)
    {
        $userEloquent = UserEloquentModel::where('email', $email)->first();

        return $userEloquent;
    }

    public function resetPassword($email, $password)
    {
        $userEloquent = UserEloquentModel::where('email', $email)->first();

        $userEloquent->update([
            "password" => $password->value
        ]);

        return $userEloquent;
    }

    public function updatePassword($userId, $password)
    {
        $userEloquent = UserEloquentModel::find($userId);

        $userEloquent->update([
            "password" => $password->value
        ]);

        return $userEloquent;
    }

    /**
     * Configure CASL usage to retrieve permissions based on the user's roles.
     *
     * This function iterates through the roles associated with the authenticated user
     * and retrieves the permissions assigned to each role. It then transforms the
     * permission names into an array of associative arrays containing "action" and "subject".
     *
     * @return array
     */
    public function getPermissionsByUserRole()
    {
        $user = auth()->user();

        $data = new PermissionsResource($user);

        return $data;
    }
}
