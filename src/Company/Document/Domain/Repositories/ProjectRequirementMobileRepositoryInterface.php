<?php

namespace Src\Company\Document\Domain\Repositories;

use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Application\DTO\ProjectRequirementData;

interface ProjectRequirementMobileRepositoryInterface
{
    public function getProjectRequirements($project_id);

    public function findRequirementById(int $id);

    public function store(ProjectRequirement $requirement);

    public function update(ProjectRequirement $projectRequirement, Request $request);

    public function delete(int $requirement_id): void;
}
