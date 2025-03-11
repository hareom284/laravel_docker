<?php

namespace Src\Company\UserManagement\Domain\Repositories;


use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;

interface UserRepositoryInterface
{
    public function getUsers($filters = []);

    public function getUsersByRole();

    public function findUserById($id);

    public function findUserInfoById($id);

    public function store(User $user, Password $password, $roleIds, $salespersonId,?Customer $customer = null, ?Staff $staff = null): User;

    public function update(User $user, $roleIds, ?Customer $customer, ?Staff $staff);

    public function updateCustomerUser($id, $user, $password);

    public function updateProfile($user, $password, $id);

    public function delete(int $user_id): void;

    public function getSelectboxUsers();

    public function getManagerList();

    public function getTeamMemberList();

    //Accounting Software Integration
    public function syncWithAccountingSoftwareData($companyId);

    public function getSurveyByUserId($id);

}
