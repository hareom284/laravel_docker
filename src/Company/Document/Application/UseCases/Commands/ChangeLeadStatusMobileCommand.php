<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;

class ChangeLeadStatusMobileCommand implements CommandInterface
{
    private RenovationDocumentMobileInterface $repository;

    public function __construct(
        private readonly int $renoDocumentId
    )
    {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->changeLeadStatus($this->renoDocumentId);
    }
}