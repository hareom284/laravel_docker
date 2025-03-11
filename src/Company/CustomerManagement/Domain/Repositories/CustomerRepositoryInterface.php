<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Domain\Model\Customer;

interface CustomerRepositoryInterface
{


    public function getCustomers($filters = []);

    public function getCustomerList($filters = []);

    public function findCustomerBySalepersonId($id, $filters = []);

    public function findCustomerByManagerId($id, $filters = []);

    public function findCustomerById($id);

    public function inactive(int $id);

    public function active(int $id);

    public function customerStore(Customer $customer, $salespersonIds);

    public function customerUpdate($user); //Need to make another function for lead update because new flow is different and also cannot update the old function because it is used in another

    public function getCustomersWithEmail();

    public function getLeadManagementReport($data);

    public function getSalepersonLeadManagementList($id, $filters = []);

    public function getGroupSalepersonLeadManagementList($mgr_id, $filters = []);

    public function getManagerLeadManagementList($id, $filters = []);

    public function updateIdMilestone($data = []);

    public function syncLeadWithQuickbook();

    public function sendSuccessMail($name, $email, $password, $siteSetting, $salespersonNames);

    public function updateCheckListStatus($data);

    public function getUsersToNotify($roles, $customer_id);

}
