<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class ChangeLeadStatusCommand implements CommandInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $renoDocumentId
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->changeLeadStatus($this->renoDocumentId);
    }
}