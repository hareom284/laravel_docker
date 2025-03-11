<?php

namespace Src\Company\Document\Domain\Repositories;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Application\DTO\DocumentData;

interface DocumentRepositoryInterface
{
    public function getDocuments($filters = []);

    public function getDocumentByProjectId(int $projectId);

    public function findDocumentById(int $id);

    public function store(Document $document): DocumentData;

    public function update(Document $document): Document;

    public function delete(int $document_id): void;

}
