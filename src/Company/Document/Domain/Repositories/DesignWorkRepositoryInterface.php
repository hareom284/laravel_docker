<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Application\DTO\DesignWorkData;

interface DesignWorkRepositoryInterface
{
    public function getDesignWorks(int $projectId);

    public function findDesignWorkById(int $id);

    public function store(DesignWork $designWork, $salepersons_id, $materials): DesignWorkData;

    public function update(DesignWork $designWork, $salepersons_id, $materials): DesignWork;

    public function delete(int $design_work_id): void;

}
