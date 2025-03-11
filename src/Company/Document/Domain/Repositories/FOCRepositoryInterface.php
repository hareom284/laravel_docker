<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;

interface FOCRepositoryInterface
{
    public function getCountLists($projectId);

    public function getFOCItems($projectId);

    public function store(RenovationDocuments $documents, array $data);

}
