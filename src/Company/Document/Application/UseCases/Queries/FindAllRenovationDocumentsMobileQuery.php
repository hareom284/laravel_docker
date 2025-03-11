<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;

class FindAllRenovationDocumentsMobileQuery implements QueryInterface
{
    private RenovationDocumentMobileInterface $repository;

    public function __construct(
        private readonly int $renovation_document_id,
        private readonly string $type
    )
    {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->getRenovationDocuments($this->renovation_document_id, $this->type);
    }
}