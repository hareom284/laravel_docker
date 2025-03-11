<?php

namespace Src\Company\Security\Domain\Services;

use Src\Company\Security\Application\Requests\StoreUserRequest;
use Src\Company\Security\Application\Mappers\UserMapper;
use Src\Company\Security\Application\Requests\UpdateUserRequest;
use Src\Company\Security\Application\UseCases\Commands\User\StoreUserCommand;
use Src\Company\Security\Application\DTO\UserData;
use Src\Company\Security\Application\UseCases\Commands\User\UpdateUserCommand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Src\Company\Security\Application\Requests\updateUserPasswordRequest;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;

class UserService
{
    public function createUser(StoreUserRequest $request)
    {
        $request->validated();
        $newUser = UserMapper::fromRequest($request);

        $createNewUser = new StoreUserCommand($newUser);
        $createNewUser->execute();
    }

    public function updateUser(UpdateUserRequest $request, $user_id)
    {
        $updateUser = UserData::fromRequest($request, $user_id);
        $updatedUserCommand = (new UpdateUserCommand($updateUser));

        $updatedUserCommand->execute();
    }

    public function deleteUser($user)
    {
        $user->delete();
    }


    public function changePassword(updateUserPasswordRequest $request)
    {
        $user = Auth::user();
        //  check passord same or not
        if (Hash::check($request->currentpassword, $user->password)) {

            UserEloquentModel::find($user->id)->update([
                "password" => $request->updatedpassword
            ]);
            return true;
        }

        return false;
    }
}
