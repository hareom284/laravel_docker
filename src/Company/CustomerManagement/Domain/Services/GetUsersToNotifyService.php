<?php
namespace Src\Company\CustomerManagement\Domain\Services;

use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class GetUsersToNotifyService
{
    public function getUsers($roles, $customerId)
    {
        
        $usersToNotify = collect();
    
        
        if (in_array("Salesperson", $roles)) {
            $salesUsers = collect($this->getUsersRelatedToSalesperson($customerId));
            $usersToNotify = $usersToNotify->merge($salesUsers);
        }
    
        
        if (in_array("Manager", $roles) || in_array("Management", $roles)) {
            $managerUsers = collect($this->getUsersRelatedToManager($customerId));
            $usersToNotify = $usersToNotify->merge($managerUsers);
        }
    
        
        if (in_array("Management", $roles)) {
            $managementUsers = collect($this->getUsersManagement());
            $usersToNotify = $usersToNotify->merge($managementUsers);
        }
    
        
        if (in_array("Marketing", $roles)) {
            $marketingUsers = collect($this->getUsersMarketing());
            $usersToNotify = $usersToNotify->merge($marketingUsers);
        }
    
        
        if (in_array("Accountant", $roles)) {
            $accountantUsers = collect($this->getUsersAccountant());
            $usersToNotify = $usersToNotify->merge($accountantUsers);
        }
    
        $usersToNotify = $usersToNotify->unique();
        return $usersToNotify;
    }
    

    public function getUsersRelatedToSalesperson($customer_id)
    {

        $customer = CustomerEloquentModel::where('id', $customer_id)->with('staffs.user')->first();

        $users = [];

        foreach ($customer->staffs as $staff) {
            array_push($users, $staff->user_id);
        }

        return $users;
    }

    public function getUsersRelatedToManager($customer_id)
    {

        $customer = CustomerEloquentModel::where('id', $customer_id)->with('staffs.mgr')->first();

        $users = [];

        foreach ($customer->staffs as $staff) {
            array_push($users, $staff->mgr_id);
        }

        return $users;
    }

    public function getUsersMarketing()
    {
        $marketings = UserEloquentModel::whereHas('roles',function($query){
            $query->where('role_id',7);
        })->get();
        $users = [];
        foreach ($marketings as $marketing) {
            array_push($users, $marketing->id);
        }
        return $users;
    }

    public function getUsersAccountant()
    {
        $accountants = UserEloquentModel::whereHas('roles',function($query){
            $query->where('role_id',4);
        })->get();
        $users = [];
        foreach ($accountants as $accountant) {
            array_push($users, $accountant->id);
        }
        return $users;
    }

    public function getUsersManagement()
    {
        $managements = UserEloquentModel::whereHas('roles',function($query){
            $query->where('role_id',2);
        })->get();
        $users = [];
        foreach ($managements as $management) {
            array_push($users, $management->id);
        }
        return $users;
    }
}
