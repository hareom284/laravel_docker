<?php

namespace Src\Company\UserManagement\Domain\Repositories;


use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;

interface UserRepositoryMobileInterface
{

    public function store(User $user,$password, $roleIds, $salespersonId,?Customer $customer = null, ?Staff $staff = null): User;

    public function findUserById($id);

    public function findUserInfoById($id);

    public function getSalepersonList();

    public function updateProfile(array $user, Password $password, int $id);

    public function updateDeviceId(array $data);

    public function storeSurveyAnswer(array $data);

    public function getSurveyAnswer();

}
