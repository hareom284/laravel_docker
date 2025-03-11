<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\DocumentStandardData;
use Src\Company\Document\Domain\Model\Entities\DocumentStandard;

interface DocumentStandardRepositoryInterface
{
    public function getDocumentStandards($filters = []);

    public function findDocumentStandardByCompanyId(int $id);

    public function findDocumentStandardById(int $id): DocumentStandardData;

    public function store(DocumentStandard $documentStandard): DocumentStandardData;

    public function update(DocumentStandard $documentStandard): DocumentStandard;

    public function delete(int $document_standard_id): void;

}
