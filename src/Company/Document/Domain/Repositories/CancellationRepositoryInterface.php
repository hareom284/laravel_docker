<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;

interface CancellationRepositoryInterface
{
    public function getCountLists($projectId);

    public function getCancellationItems($projectId);

    public function store(RenovationDocuments $documents, array $data);

}
