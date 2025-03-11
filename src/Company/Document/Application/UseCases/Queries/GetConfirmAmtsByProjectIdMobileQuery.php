<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;

class GetConfirmAmtsByProjectIdMobileQuery implements QueryInterface
{
    private RenovationDocumentMobileInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
    }

    public function handle()
    {
        // authorize('findDocumentById', DocumentPolicy::class);
        return $this->repository->getConfirmAmtsByProjectId($this->projectId);
    }
}