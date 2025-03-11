<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\EvoData;
use Src\Company\Document\Domain\Model\Entities\Evo;

interface EvoRepositoryInterface
{
    public function getEvoAmt(int $projectId);

    public function findEvoByProjectId($projectId);

    public function findEvoById($evo_id);

    public function store(Evo $evo): EvoData;

    public function customerSign($request);

    public function getDocumentStandard($project_id);
}
