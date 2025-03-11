<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\ElectricalPlansData;
use Src\Company\Document\Domain\Model\Entities\ElectricalPlans;

interface ElectricalPlansRepositoryInterface
{
    public function getElectricalPlans(int $projectId);

    // public function findDesignWorkById(int $id);

    public function store(ElectricalPlans $electricalPlans, $salesperson_id, $materials): ElectricalPlansData;

    // public function update(DesignWork $designWork, $salepersons_id, $materials): DesignWork;

    public function delete(int $electrical_plans_id): void;

}
