<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Domain\Model\Entities\ThreeDDesign;
use Src\Company\Document\Application\DTO\ThreeDDesignData;

interface ThreeDDesignRepositoryInterface
{

    public function getThreeDDesignByProjectId(int $projectId);

    public function getThreeDDesignById(int $id);

    public function store(ThreeDDesign $threeDDesign);

    public function update(ThreeDDesign $threeDDesign);

    public function delete(int $id);

    // public function getDesignWorks(int $projectId);

    // public function findDesignWorkById(int $id);

    // public function store(DesignWork $designWork, $salepersons_id, $materials): DesignWorkData;

    // public function update(DesignWork $designWork, $salepersons_id, $materials): DesignWork;

    // public function delete(int $design_work_id): void;

}
