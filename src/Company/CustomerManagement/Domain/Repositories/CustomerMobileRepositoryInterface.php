<?php

namespace Src\Company\CustomerManagement\Domain\Repositories;

use Src\Company\CustomerManagement\Domain\Model\Customer;

interface CustomerMobileRepositoryInterface
{

    public function findCustomerBySalepersonId($id, $filters = []);

    public function findCustomerById($id);

    public function customerStore(Customer $customer, $salespersonIds);

    public function customerUpdate($user); //Need to make another function for lead update because new flow is different and also cannot update the old function because it is used in another

    public function getCustomerListWithProperties();


}
