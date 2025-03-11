<?php

namespace Src\Auth\Domain\Repositories;

interface AuthRepositoryInterface
{
    //login
    public function login($credentials);

    //verification email
    public function verification($id);

    public function findUserByEmail($email);

    public function resetPassword($email,$password);

    public function updatePassword($userId,$password);


    public function getPermissionsByUserRole();

}
