<?php

namespace Src\Company\System\Domain\Repositories;


interface UserRepositoryInterface
{

    public function getCustomers($filters = []);

    public function getRanks();


    public function getSalepersonList($filters = []);

    public function getSalepersonReportList();

    public function getCustomerList($filters = []);

    public function getDrafters();

    public function findCustomerBySalepersonId($id, $filters = []);

    public function findCustomerByManagerId($id, $filters = []);

    public function findCustomerById($id);

    public function inactive(int $id): void;//

    public function active(int $id);//


    public function customerUpdate($id, $user, $password); //Need to make another function for lead update because new flow is different and also cannot update the old function because it is used in another

    public function salepersonNotifyMail($salepersonName, $salepersonEmail, $customerName);

    public function assignRank(int $salepersonId, int $rankId): void;

    public function sendSuccessMail($name, $email, $password, $siteSetting, $salespersonNames);

    public function updateCheckListStatus($data);


    public function getCustomersWithEmail();

    public function getCampaingList($filters);

    public function getLeadManagementReport($data);

    public function getSalepersonLeadManagementList($id, $filters = []);

    public function getDesignerListsForVendorFilter();


    public function getManagementOrManger();

    public function getGroupSalepersonLeadManagementList($mgr_id, $filters = []);

    public function getManagerLeadManagementList($id, $filters = []);

    public function updateIdMilestone($data = []);

    public function syncLeadWithQuickbook();
}
