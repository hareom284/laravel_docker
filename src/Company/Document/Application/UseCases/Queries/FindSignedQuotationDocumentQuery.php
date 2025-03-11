<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class FindSignedQuotationDocumentQuery implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $project_id,
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        return $this->repository->signedQuotationDocument($this->project_id);
    }
}