<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class FindAllRenovationDocumentsQuery implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $renovation_document_id,
        private readonly string $type
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getRenovationDocuments($this->renovation_document_id, $this->type);
    }
}