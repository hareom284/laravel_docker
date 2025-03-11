<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Application\DTO\ProjectData;

interface ProjectRepositoryInterface
{
    public function getProjects();

    public function getProjectLists();

    public function getProjectListsForOthers($filters);

    public function getProjectListForSaleperson($filters);

    public function getProjectsForAccountant($perPage, $salePerson,$filterText, $status, $created_at);

    public function getInProgressProjects();

    public function getProjectsForManagement($perPage, $salePerson,$filterText,$status,$created_at);

    public function projectByCustomerId(int $customerId);

    public function store(Project $project, $property_id, $salespersonIds, $document, $block_num): ProjectData;

    public function update(Project $project, $salespersonIds, $agreement_no, $customerIds, $id);

    public function destroy(int $project_id): void;

    public function show(int $project_id);

    public function sendMailToCustomer(int $projectId, $customerName, $customerEmail, $customerPassword);

    public function projectDetailForHandover(int $projectId);

    public function getCompanyStampByProjectId(int $projectId);

    public function findOngoingProjects();

    public function cancelProject($projectId);
    public function retrieveProject($projectId);

    public function ToggleFreezedProjectById($projectId);
    
    public function getNewProjectList($perPage, $salePerson, $companyId, $filterText, $status, $filters);

    public function getPendingCancelProjects($perPage);
    
    public function pendingCancelProject($id);

}
