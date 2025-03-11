<?php

namespace Src\Company\Project\Domain\Repositories;

use Illuminate\Http\Request;
use Src\Company\Project\Application\DTO\ProjectData;
use Src\Company\Project\Domain\Model\Entities\Project;

interface ProjectMobileRepositoryInterface
{

    public function getProjectListForSaleperson($filters);

    public function store(Project $project, Request $request);

    public function update(Project $project, Request $request, $id);

    public function sendMailToCustomer(int $projectId, $customerName, $customerEmail, $customerPassword);

    public function show(int $project_id);

    public function getCompanyStampByProjectId(int $projectId);

    public function projectDetailForHandover(int $projectId);

    public function getProjectReport(int $projectId);

    public function getProjectsForManagement($perPage, $salePerson,$filterText,$status,$created_at,$cardView);

}
