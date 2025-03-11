<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;

interface VariationOrderMobileRepositoryInterface
{
    public function getVariationItems($projectId, $saleperson_id);

    public function getUpdateVariationItems($documentId, $saleperson_id);

    public function store(RenovationDocuments $documents, array $data);
}