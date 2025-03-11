<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;

class FindTemplateItemsForUpdateMobileQuery implements QueryInterface
{
    private RenovationDocumentMobileInterface $repository;

    public function __construct(
        private readonly int $document_id
    )
    {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->findTemplateItemsForUpdate($this->document_id);
    }
}