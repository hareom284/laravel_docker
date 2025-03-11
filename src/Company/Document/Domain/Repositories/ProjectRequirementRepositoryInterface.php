<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Application\DTO\ProjectRequirementData;

interface ProjectRequirementRepositoryInterface
{
    public function getProjectRequirements($project_id);

    public function findRequirementById(int $id): ProjectRequirementData;

    public function store(ProjectRequirement $requirement);

    public function update(array $data);

    public function delete(int $requirement_id): void;
}
